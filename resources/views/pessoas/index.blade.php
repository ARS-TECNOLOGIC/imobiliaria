@extends('layouts.app')

@section('titulo', 'Pessoas')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Pessoas</h1>
        <a href="{{ route('pessoas.create') }}" class="btn btn-primary">Nova pessoa</a>
    </div>

    <table class="table table-striped align-middle">
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
                <tr>
                    <td>{{ $pessoa->nome }}</td>
                    <td>{{ $pessoa->cpf_cnpj }}</td>
                    <td>{{ $pessoa->email ?? '—' }}</td>
                    <td>{{ $pessoa->cidade ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('pessoas.show', $pessoa) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('pessoas.edit', $pessoa) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('pessoas.destroy', $pessoa) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Remover esta pessoa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Nenhuma pessoa cadastrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $pessoas->links() }}
@endsection
