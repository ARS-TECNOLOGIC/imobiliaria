@extends('layouts.app')

@section('titulo', 'Imóveis')

@section('conteudo')
    <div class="ds-page-header-row">
        <div class="ds-page-icon ds-page-icon--ink">
            <i class="fa-solid fa-building"></i>
        </div>
        <div class="ds-page-info">
            <h1 class="ds-page-title">Imóveis</h1>
            <p class="ds-page-subtitle">Cadastre e gerencie imóveis</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form method="GET" class="ds-search flex-grow-1">
            <div class="input-group">
                <input type="text" name="busca" value="{{ $busca }}" class="form-control"
                       placeholder="Buscar por código, endereço, cidade ou locador...">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
                @if ($busca)
                    <a href="{{ route('imoveis.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark"></i> Limpar
                    </a>
                @endif
            </div>
        </form>
        <a href="{{ route('imoveis.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Novo imóvel
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-listing">
            <thead>
                <tr>
                    <th>Imóvel</th>
                    <th>Endereço</th>
                    <th>Locador</th>
                    <th>Tipo</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($imoveis as $imovel)
                    @php
                        $endereco = collect([$imovel->logradouro, $imovel->numero])->filter()->implode(', ');
                        $bairro = $imovel->bairro ?? '';
                        $cidadeUf = collect([$imovel->cidade, $imovel->uf])->filter()->implode('/');
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-imovel">
                                <div class="icon-box"><i class="fa-solid fa-house"></i></div>
                                <span class="imovel-codigo">{{ $imovel->codigo }}</span>
                            </div>
                        </td>
                        <td class="cell-text" title="{{ $endereco }}{{ $bairro ? ', ' . $bairro : '' }}">
                            {{ $endereco ?: '—' }}{{ $bairro ? ', ' . $bairro : '' }}
                        </td>
                        <td>
                            <div class="cell-pessoa">
                                <i class="fa-regular fa-user"></i>
                                <span class="pessoa-nome" title="{{ $imovel->locador->nome ?? '' }}">
                                    {{ $imovel->locador->nome ?? '—' }}
                                </span>
                            </div>
                        </td>
                        <td class="cell-text">{{ ucfirst(strtolower($imovel->tipo->value)) }}</td>
                        <td class="col-acoes">
                            <div class="acoes-cell">
                                <a href="{{ route('imoveis.show', $imovel) }}" class="btn-icon view" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('imoveis.edit', $imovel) }}" class="btn-icon edit" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('imoveis.destroy', $imovel) }}" method="POST"
                                      onsubmit="return confirm('Remover este imóvel?');">
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
                                Nenhum imóvel encontrado para "{{ $busca }}".
                            @else
                                Nenhum imóvel cadastrado.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($imoveis->hasPages())
        <div class="ds-pagination">
            <span class="ds-pagination-info">
                Mostrando {{ $imoveis->firstItem() }}–{{ $imoveis->lastItem() }} de {{ $imoveis->total() }} imóveis
            </span>
            {{ $imoveis->links() }}
        </div>
    @endif
@endsection
