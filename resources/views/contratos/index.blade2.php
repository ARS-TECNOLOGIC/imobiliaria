@extends('layouts.app')

@section('titulo', 'Contratos')

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Contratos</h1>
        <a href="{{ route('contratos.create') }}" class="btn btn-primary">Novo contrato</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="busca" value="{{ $busca }}" class="form-control"
                   placeholder="Buscar por código, locador ou locatário...">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
        </div>
        @if ($busca)
            <div class="col-md-2">
                <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
            </div>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-striped align-middle table-contratos">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th class="col-imovel">Imóvel</th>
                    <th class="col-pessoa">Locador</th>
                    <th class="col-pessoa">Locatário</th>
                    <th class="col-valores">Valores</th>
                    <th class="col-total">Total</th>
                    <th class="col-status">Status</th>
                    <th class="col-acoes text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contratos as $contrato)
                    @php
                        $locador = $contrato->partes->first(fn($p) => $p->papel->value === 'LOCADOR_TITULAR');
                        $locatario = $contrato->partes->first(fn($p) => $p->papel->value === 'LOCATARIO_TITULAR');
                        $valorTotal = $contrato->valor_aluguel + $contrato->valor_condominio + $contrato->valor_iptu + $contrato->valor_seguro;
                    @endphp
                    <tr>
                        <td class="col-id">{{ $contrato->id }}</td>
                        <td class="col-imovel">{{ $contrato->imovel->codigo }}</td>
                        <td class="col-pessoa">
                            <span class="pessoa-truncate" title="{{ $locador->pessoa?->nome ?? '' }}">
                                {{ $locador->pessoa?->nome ?? '—' }}
                            </span>
                        </td>
                        <td class="col-pessoa">
                            <span class="pessoa-truncate" title="{{ $locatario->pessoa?->nome ?? '' }}">
                                {{ $locatario->pessoa?->nome ?? '—' }}
                            </span>
                        </td>
                        <td class="col-valores">
                            <span class="valores-item">Aluguel: R$ {{ number_format($contrato->valor_aluguel, 2, ',', '.') }}</span>
                            <span class="valores-item">Cond.: R$ {{ number_format($contrato->valor_condominio, 2, ',', '.') }}</span>
                            <span class="valores-item">IPTU: R$ {{ number_format($contrato->valor_iptu, 2, ',', '.') }}</span>
                            <span class="valores-item">Seguro: R$ {{ number_format($contrato->valor_seguro, 2, ',', '.') }}</span>
                        </td>
                        <td class="col-total">R$ {{ number_format($valorTotal, 2, ',', '.') }}</td>
                        <td class="col-status">
                            @php
                                $corStatus = match ($contrato->status->value) {
                                    'ATIVO' => 'success',
                                    'SUSPENSO' => 'warning',
                                    'ENCERRADO' => 'secondary',
                                    default => 'info',
                                };
                            @endphp
                            <span class="badge bg-{{ $corStatus }}">{{ $contrato->status->value }}</span>
                        </td>
                        <td class="col-acoes">
                            <div class="acoes-cell">
                                <a href="{{ route('contratos.show', $contrato) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('contratos.edit', $contrato) }}"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('contratos.destroy', $contrato) }}" method="POST"
                                      onsubmit="return confirm('Remover este contrato?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
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

    {{ $contratos->links() }}
@endsection
