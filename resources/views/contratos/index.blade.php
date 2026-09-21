@extends('layouts.app')

@section('titulo', 'Contratos')

@section('conteudo')
    <div class="ds-page-header-row">
        <div class="ds-page-icon ds-page-icon--brand">
            <i class="fa-solid fa-file-contract"></i>
        </div>
        <div class="ds-page-info">
            <h1 class="ds-page-title">Contratos</h1>
            <p class="ds-page-subtitle">Gerencie os contratos de locação</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form method="GET" class="ds-search flex-grow-1">
            <div class="input-group">
                <input type="text" name="busca" value="{{ $busca }}" class="form-control"
                       placeholder="Buscar por código, locador ou locatário...">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
                @if ($busca)
                    <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark"></i> Limpar
                    </a>
                @endif
            </div>
        </form>
        <a href="{{ route('contratos.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Novo contrato
        </a>
    </div>

    @php
        $garantiaLabels = [
            'FIADOR' => 'Fiador',
            'SEGURO_FIANCA' => 'Seguro fiança',
            'TITULO_CAPITALIZACAO' => 'Título capitalização',
            'CAUCAO' => 'Caução',
            'SEM_GARANTIA' => 'Sem garantia',
        ];
    @endphp

    <div class="table-responsive">
        <table class="table table-hover align-middle table-listing table-contratos">
            <colgroup>
                <col class="col-id">
                <col class="col-imovel">
                <col class="col-locador">
                <col class="col-locatario">
                <col class="col-total">
                <col class="col-status">
                <col class="col-acoes">
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Imóvel</th>
                    <th>Locador</th>
                    <th>Locatário</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contratos as $contrato)
                    @php
                        $locador = $contrato->partes->first(fn($p) => $p->papel->value === 'LOCADOR_TITULAR');
                        $locatario = $contrato->partes->first(fn($p) => $p->papel->value === 'LOCATARIO_TITULAR');
                        $valorTotal = $contrato->valor_aluguel + $contrato->valor_condominio + $contrato->valor_iptu + $contrato->valor_seguro;

                        $corStatus = match ($contrato->status->value) {
                            'ATIVO' => 'ativo',
                            'SUSPENSO' => 'suspenso',
                            'ENCERRADO' => 'encerrado',
                            default => 'pendente',
                        };
                    @endphp
                    <tr>
                        <td class="cell-id">{{ $contrato->id }}</td>
                        <td>
                            <div class="cell-imovel">
                                <div class="icon-box"><i class="fa-solid fa-house"></i></div>
                                <span class="imovel-codigo">{{ $contrato->imovel->codigo }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="cell-pessoa">
                                <i class="fa-regular fa-user"></i>
                                <span class="pessoa-nome" title="{{ $locador->pessoa?->nome ?? '' }}">
                                    {{ $locador->pessoa?->nome ?? '—' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="cell-pessoa">
                                <i class="fa-regular fa-user"></i>
                                <span class="pessoa-nome" title="{{ $locatario->pessoa?->nome ?? '' }}">
                                    {{ $locatario->pessoa?->nome ?? '—' }}
                                </span>
                            </div>
                        </td>
                        <td class="cell-total">R$ {{ number_format($valorTotal, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge-status {{ $corStatus }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $contrato->status->value }}
                            </span>
                        </td>
                        <td class="col-acoes">
                            <div class="acoes-cell">
                                <button class="btn-icon expand" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#contrato-detalhes-{{ $contrato->id }}"
                                        aria-expanded="false" title="Detalhes">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <a href="{{ route('contratos.show', $contrato) }}" class="btn-icon view" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('contratos.edit', $contrato) }}" class="btn-icon edit" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('contratos.destroy', $contrato) }}" method="POST"
                                      onsubmit="return confirm('Remover este contrato?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Remover">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr class="collapse details-row" id="contrato-detalhes-{{ $contrato->id }}">
                        <td colspan="7">
                            <div class="list-details">
                                <div class="list-details__grid">
                                    <span class="list-details__item">
                                        <i class="fa-solid fa-calendar"></i>
                                        Início: <strong>{{ $contrato->data_inicio->format('d/m/Y') }}</strong>
                                    </span>
                                    @if ($contrato->data_fim)
                                        <span class="list-details__item">
                                            <i class="fa-solid fa-calendar-xmark"></i>
                                            Fim: <strong>{{ $contrato->data_fim->format('d/m/Y') }}</strong>
                                        </span>
                                    @endif
                                    <span class="list-details__item">
                                        <i class="fa-solid fa-calendar-day"></i>
                                        Vencimento: <strong>dia {{ $contrato->dia_vencimento }}</strong>
                                    </span>
                                    <span class="list-details__item">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                        Garantia: <strong>{{ $garantiaLabels[$contrato->garantia->value] ?? $contrato->garantia->value }}</strong>
                                    </span>
                                    <span class="list-details__item">
                                        <i class="fa-solid fa-percent"></i>
                                        Taxa adm: <strong>{{ number_format($contrato->taxa_adm_percentual, 1, ',', '.') }}%</strong>
                                    </span>
                                    <span class="list-details__item">
                                        <i class="fa-solid fa-gavel"></i>
                                        Multa: <strong>{{ number_format($contrato->multa_percentual, 1, ',', '.') }}%</strong>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            @if ($busca)
                                Nenhum contrato encontrado para "{{ $busca }}".
                            @else
                                Nenhum contrato cadastrado.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($contratos->hasPages())
        <div class="ds-pagination">
            <span class="ds-pagination-info">
                Mostrando {{ $contratos->firstItem() }}–{{ $contratos->lastItem() }} de {{ $contratos->total() }} contratos
            </span>
            {{ $contratos->links() }}
        </div>
    @endif
@endsection
