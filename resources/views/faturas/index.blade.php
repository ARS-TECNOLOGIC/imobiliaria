@extends('layouts.app')

@section('titulo', 'Faturas')

@section('conteudo')
    <div class="ds-page-header-row">
        <div class="ds-page-icon ds-page-icon--warning">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="ds-page-info">
            <h1 class="ds-page-title">Faturas</h1>
            <p class="ds-page-subtitle">Cobranças mensais dos contratos</p>
        </div>
    </div>

    <div class="mb-4">
        <form method="GET">
            <div class="row g-2 mb-2">
                <div class="col-md-5">
                    <input type="text" name="busca" value="{{ $busca }}" class="form-control"
                           placeholder="Buscar por contrato, código do imóvel ou mês/ano...">
                </div>
                <div class="col-md-3">
                    <select name="status_pagamento" class="form-select">
                        <option value="">Todos os status</option>
                        <option value="PENDENTE" {{ $status === 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                        <option value="PAGO" {{ $status === 'PAGO' ? 'selected' : '' }}>Pago</option>
                        <option value="PAGO_COM_ATRASO" {{ $status === 'PAGO_COM_ATRASO' ? 'selected' : '' }}>Pago com atraso</option>
                        <option value="ATRASADO" {{ $status === 'ATRASADO' ? 'selected' : '' }}>Atrasado</option>
                        <option value="CANCELADO" {{ $status === 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <a href="{{ route('faturas.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark"></i> Limpar
                    </a>
                    <a href="{{ route('faturas.create') }}" class="btn btn-primary ms-auto">
                        <i class="fa-solid fa-plus"></i> Nova fatura
                    </a>
                </div>
            </div>
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Vencimento de</label>
                    <input type="date" name="vencimento_de" value="{{ $vencimentoDe }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Vencimento até</label>
                    <input type="date" name="vencimento_ate" value="{{ $vencimentoAte }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-listing">
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Contrato</th>
                    <th>Vencimento</th>
                    <th>Aluguel</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($faturas as $fatura)
                    @php
                        $cor = match ($fatura->status_pagamento->value) {
                            'PAGO' => 'pago',
                            'PAGO_COM_ATRASO' => 'pago-com-atraso',
                            'ATRASADO' => 'atrasado',
                            'CANCELADO' => 'cancelado',
                            default => 'pendente',
                        };
                        $statusLabel = str_replace('_', ' ', $fatura->status_pagamento->value);
                        $valorTotal = $fatura->valor_aluguel + $fatura->valor_condominio + $fatura->valor_iptu + $fatura->valor_seguro
                                    + $fatura->valor_taxa_extra - $fatura->valor_desconto + $fatura->valor_multa_juros;
                    @endphp
                    <tr>
                        <td class="cell-id">{{ $fatura->referencia->format('m/Y') }}</td>
                        <td class="cell-text" title="Contrato #{{ $fatura->contrato_id }}">
                            #{{ $fatura->contrato_id }} — {{ $fatura->contrato->imovel->codigo }}
                        </td>
                        <td class="cell-text">{{ $fatura->data_vencimento->format('d/m/Y') }}</td>
                        <td class="cell-total" style="font-size:14px;">R$ {{ number_format($fatura->valor_aluguel, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge-status {{ $cor }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="col-acoes">
                            <div class="acoes-cell">
                                <button class="btn-icon expand" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#fatura-detalhes-{{ $fatura->id }}"
                                        aria-expanded="false" title="Detalhes">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <a href="{{ route('faturas.show', $fatura) }}" class="btn-icon view" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('faturas.edit', $fatura) }}" class="btn-icon edit" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('faturas.destroy', $fatura) }}" method="POST"
                                      onsubmit="return confirm('Remover esta fatura e seu repasse?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Remover">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr class="collapse details-row" id="fatura-detalhes-{{ $fatura->id }}">
                        <td colspan="6">
                            <div class="list-details">
                                <div class="list-details__grid">
                                    @if ($fatura->valor_condominio > 0)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-building"></i>
                                            Cond.: <strong>R$ {{ number_format($fatura->valor_condominio, 2, ',', '.') }}</strong>
                                        </span>
                                    @endif
                                    @if ($fatura->valor_iptu > 0)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-landmark"></i>
                                            IPTU: <strong>R$ {{ number_format($fatura->valor_iptu, 2, ',', '.') }}</strong>
                                        </span>
                                    @endif
                                    @if ($fatura->valor_seguro > 0)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-shield-halved"></i>
                                            Seguro: <strong>R$ {{ number_format($fatura->valor_seguro, 2, ',', '.') }}</strong>
                                        </span>
                                    @endif
                                    <span class="list-details__item">
                                        <i class="fa-solid fa-calculator"></i>
                                        Total: <strong>R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong>
                                    </span>
                                    @if ($fatura->dias_atraso > 0)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-clock"></i>
                                            {{ $fatura->dias_atraso }} {{ $fatura->dias_atraso === 1 ? 'dia' : 'dias' }} de atraso
                                        </span>
                                    @endif
                                    @if ($fatura->valor_pago > 0)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Pago: <strong>R$ {{ number_format($fatura->valor_pago, 2, ',', '.') }}</strong>
                                        </span>
                                    @endif
                                    @if ($fatura->data_recebimento)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-calendar-check"></i>
                                            Recebido: <strong>{{ $fatura->data_recebimento->format('d/m/Y') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            @if ($busca || $vencimentoDe || $vencimentoAte || $status)
                                Nenhuma fatura encontrada para os filtros informados.
                            @else
                                Nenhuma fatura cadastrada.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($faturas->hasPages())
        <div class="ds-pagination">
            <span class="ds-pagination-info">
                @if ($busca || $vencimentoDe || $vencimentoAte || $status)
                    {{ $faturas->total() }} {{ $faturas->total() === 1 ? 'fatura encontrada' : 'faturas encontradas' }}
                @else
                    Mostrando {{ $faturas->firstItem() }}–{{ $faturas->lastItem() }} de {{ $faturas->total() }} faturas
                @endif
            </span>
            {{ $faturas->links() }}
        </div>
    @endif
@endsection
