@extends('layouts.app')

@section('titulo', 'Faturas')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Faturas</h1>
        <a href="{{ route('faturas.create') }}" class="btn btn-primary">Nova fatura</a>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Referência</th>
                <th>Contrato</th>
                <th>Vencimento</th>
                <th>Valor aluguel</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($faturas as $fatura)
                <tr>
                    <td>{{ $fatura->referencia->format('m/Y') }}</td>
                    <td>Contrato #{{ $fatura->contrato_id }} — {{ $fatura->contrato->imovel->codigo }}</td>
                    <td>{{ $fatura->data_vencimento->format('d/m/Y') }}</td>
                    <td>R$ {{ number_format($fatura->valor_aluguel, 2, ',', '.') }}</td>
                    <td>
                        @php
                            $cor = match ($fatura->status_pagamento->value) {
                                'PAGO' => 'success',
                                'PAGO_COM_ATRASO' => 'warning',
                                'ATRASADO' => 'danger',
                                'CANCELADO' => 'dark',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $cor }}">{{ str_replace('_', ' ', $fatura->status_pagamento->value) }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('faturas.show', $fatura) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('faturas.edit', $fatura) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('faturas.destroy', $fatura) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Remover esta fatura e seu repasse?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Nenhuma fatura cadastrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $faturas->links() }}
@endsection
