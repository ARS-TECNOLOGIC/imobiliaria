@extends('layouts.app')

@section('titulo', 'Imóveis')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Imóveis</h1>
        <a href="{{ route('imoveis.create') }}" class="btn btn-primary">Novo imóvel</a>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Código</th>
                <th>Endereço</th>
                <th>Locador</th>
                <th>Tipo</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($imoveis as $imovel)
                <tr>
                    <td>{{ $imovel->codigo }}</td>
                    <td>{{ $imovel->logradouro }}, {{ $imovel->numero }} — {{ $imovel->cidade }}/{{ $imovel->uf }}</td>
                    <td>{{ $imovel->locador->nome }}</td>
                    <td>{{ ucfirst(strtolower($imovel->tipo->value)) }}</td>
                    <td class="text-end">
                        <a href="{{ route('imoveis.show', $imovel) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('imoveis.edit', $imovel) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('imoveis.destroy', $imovel) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Remover este imóvel?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Nenhum imóvel cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $imoveis->links() }}
@endsection
