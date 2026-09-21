@extends('layouts.app')

@section('titulo', 'Fatura #' . $fatura->id)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Fatura {{ $fatura->referencia->format('m/Y') }} — Contrato #{{ $fatura->contrato_id }}</h1>
        <div>
            <a href="{{ route('faturas.edit', $fatura) }}" class="btn btn-outline-primary">Editar</a>
            <a href="{{ route('faturas.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <h2 class="h5">Dados da fatura</h2>
            <dl class="row">
                <dt class="col-sm-5">Imóvel</dt>
                <dd class="col-sm-7">{{ $fatura->contrato->imovel->codigo }}</dd>

                <dt class="col-sm-5">Favorecido</dt>
                <dd class="col-sm-7">{{ $fatura->contrato->favorecido?->nome ?? 'Pessoa removida' }}</dd>

                <dt class="col-sm-5">Vencimento</dt>
                <dd class="col-sm-7">{{ $fatura->data_vencimento->format('d/m/Y') }}</dd>

                <dt class="col-sm-5">Aluguel / Condomínio / IPTU / Seguro</dt>
                <dd class="col-sm-7">
                    R$ {{ number_format($fatura->valor_aluguel, 2, ',', '.') }} /
                    R$ {{ number_format($fatura->valor_condominio, 2, ',', '.') }} /
                    R$ {{ number_format($fatura->valor_iptu, 2, ',', '.') }} /
                    R$ {{ number_format($fatura->valor_seguro, 2, ',', '.') }}
                </dd>

                <dt class="col-sm-5">Taxa extra / Desconto / Multa</dt>
                <dd class="col-sm-7">
                    R$ {{ number_format($fatura->valor_taxa_extra, 2, ',', '.') }} /
                    R$ {{ number_format($fatura->valor_desconto, 2, ',', '.') }} /
                    R$ {{ number_format($fatura->valor_multa_juros, 2, ',', '.') }}
                    @if ($fatura->isento_multa)
                        <span class="badge bg-highlight text-dark">isenta</span>
                    @endif
                </dd>

                @php
                    $total = $fatura->valor_aluguel + $fatura->valor_condominio + $fatura->valor_iptu
                        + $fatura->valor_seguro + $fatura->valor_taxa_extra - $fatura->valor_desconto
                        + $fatura->valor_multa_juros;
                @endphp
                <dt class="col-sm-5">Total da fatura</dt>
                <dd class="col-sm-7"><strong>R$ {{ number_format($total, 2, ',', '.') }}</strong></dd>

                <dt class="col-sm-5">Status</dt>
                <dd class="col-sm-7">
                    @php
                        $corShow = match ($fatura->status_pagamento->value) {
                            'PAGO' => 'success',
                            'PAGO_COM_ATRASO' => 'highlight',
                            'ATRASADO' => 'danger',
                            'CANCELADO' => 'dark',
                            default => 'info',
                        };
                    @endphp
                    <span class="badge bg-{{ $corShow }}">{{ str_replace('_', ' ', $fatura->status_pagamento->value) }}</span>
                </dd>

                <dt class="col-sm-5">Valor pago</dt>
                <dd class="col-sm-7">{{ $fatura->valor_pago ? 'R$ ' . number_format($fatura->valor_pago, 2, ',', '.') : '—' }}</dd>
            </dl>
        </div>

        <div class="col-md-6">
            <h2 class="h5">Repasse ao locador</h2>
            @if ($fatura->repasse)
                <dl class="row mb-3">
                    <dt class="col-sm-5">Taxa de administração</dt>
                    <dd class="col-sm-7">R$ {{ number_format($fatura->repasse->valor_taxa_adm, 2, ',', '.') }}</dd>

                    <dt class="col-sm-5">Total do repasse</dt>
                    <dd class="col-sm-7"><strong>R$ {{ number_format($fatura->repasse->valor_total_repasse, 2, ',', '.') }}</strong></dd>

                    <dt class="col-sm-5">Data limite</dt>
                    <dd class="col-sm-7">{{ $fatura->repasse->data_limite_repasse->format('d/m/Y') }}</dd>

                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-{{ $fatura->repasse->status->value === 'EFETUADO' ? 'success' : 'secondary' }}">
                            {{ $fatura->repasse->status->value }}
                        </span>
                    </dd>

                    <dt class="col-sm-5">Líquido da administradora</dt>
                    <dd class="col-sm-7">
                        R$ {{ number_format($fatura->repasse->valor_taxa_adm - $fatura->repasse->valor_custos, 2, ',', '.') }}
                        <div class="form-text mb-0">Taxa de adm. − custos. Não afeta o repasse ao locador, é só informativo.</div>
                    </dd>
                </dl>

                <form action="{{ route('repasses.update', $fatura->repasse) }}" method="POST" class="row g-2">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label">Valor retido</label>
                        <input type="number" step="0.01" name="valor_retido" value="{{ $fatura->repasse->valor_retido }}" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Motivo da retenção</label>
                        <input type="text" name="descricao_retido" value="{{ $fatura->repasse->descricao_retido }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Custos</label>
                        <input type="number" step="0.01" name="valor_custos" value="{{ $fatura->repasse->valor_custos }}" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Descrição dos custos</label>
                        <input type="text" name="descricao_custos" value="{{ $fatura->repasse->descricao_custos }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status do repasse</label>
                        <select name="status" class="form-select">
                            @foreach (\App\Enums\StatusRepasse::cases() as $opcao)
                                <option value="{{ $opcao->value }}" @selected($fatura->repasse->status->value === $opcao->value)>
                                    {{ $opcao->value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Data do repasse</label>
                        <input type="date" name="data_repasse" value="{{ $fatura->repasse->data_repasse?->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-outline-primary">Atualizar repasse</button>
                    </div>
                </form>
            @else
                <p class="text-muted">Esta fatura não possui repasse (situação inesperada — confira o cadastro).</p>
            @endif
        </div>
    </div>
@endsection
