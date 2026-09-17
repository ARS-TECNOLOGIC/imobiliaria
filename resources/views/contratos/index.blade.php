@extends('layouts.app')

@section('titulo', 'Contratos')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Contratos</h1>
        <a href="{{ route('contratos.create') }}" class="btn btn-primary">Novo contrato</a>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Imóvel</th>
                <th>Favorecido</th>
                <th>Valor aluguel</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contratos as $contrato)
                <tr>
                    <td>{{ $contrato->id }}</td>
                    <td>{{ $contrato->imovel->codigo }}</td>
                    <td>{{ $contrato->favorecido?->nome ?? 'Pessoa removida' }}</td>
                    <td>R$ {{ number_format($contrato->valor_aluguel, 2, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $contrato->status->value === 'ATIVO' ? 'success' : 'secondary' }}">
                            {{ $contrato->status->value }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('contratos.destroy', $contrato) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Remover este contrato?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Nenhum contrato cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $contratos->links() }}
@endsection
