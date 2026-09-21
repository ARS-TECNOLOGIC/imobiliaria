@extends('layouts.app')

@section('titulo', 'Pessoas')

@section('conteudo')
    <div class="ds-page-header-row">
        <div class="ds-page-icon ds-page-icon--success">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="ds-page-info">
            <h1 class="ds-page-title">Pessoas</h1>
            <p class="ds-page-subtitle">Locadores, locatários, fiadores e corretores</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form method="GET" class="ds-search flex-grow-1">
            <div class="input-group">
                <input type="text" name="busca" value="{{ $busca }}" class="form-control"
                       placeholder="Buscar por nome, CPF/CNPJ, e-mail ou cidade...">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
                @if ($busca)
                    <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark"></i> Limpar
                    </a>
                @endif
            </div>
        </form>
        <a href="{{ route('pessoas.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nova pessoa
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-listing">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF/CNPJ</th>
                    <th>E-mail</th>
                    <th>Cidade</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pessoas as $pessoa)
                    @php
                        $cidadeUf = collect([$pessoa->cidade, $pessoa->uf])->filter()->implode('/');
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-pessoa">
                                <i class="fa-regular fa-user"></i>
                                <span class="pessoa-nome">{{ $pessoa->nome }}</span>
                            </div>
                        </td>
                        <td class="cell-text">{{ $pessoa->cpf_cnpj }}</td>
                        <td class="cell-text{{ $pessoa->email ? '' : ' cell-text--muted' }}">
                            {{ $pessoa->email ?? '—' }}
                        </td>
                        <td class="cell-text">{{ $cidadeUf ?: '—' }}</td>
                        <td class="col-acoes">
                            <div class="acoes-cell">
                                <a href="{{ route('pessoas.show', $pessoa) }}" class="btn-icon view" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('pessoas.edit', $pessoa) }}" class="btn-icon edit" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('pessoas.destroy', $pessoa) }}" method="POST"
                                      onsubmit="return confirm('Remover esta pessoa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Remover">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            @if ($busca)
                                Nenhuma pessoa encontrada para "{{ $busca }}".
                            @else
                                Nenhuma pessoa cadastrada.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($pessoas->hasPages())
        <div class="ds-pagination">
            <span class="ds-pagination-info">
                Mostrando {{ $pessoas->firstItem() }}–{{ $pessoas->lastItem() }} de {{ $pessoas->total() }} pessoas
            </span>
            {{ $pessoas->links() }}
        </div>
    @endif
@endsection
