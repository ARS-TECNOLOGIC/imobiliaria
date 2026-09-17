@extends('layouts.app')

@section('titulo', 'Contrato #' . $contrato->id)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">
            Contrato #{{ $contrato->id }}
            <span class="badge bg-{{ $contrato->status->value === 'ATIVO' ? 'success' : 'secondary' }}">
                {{ $contrato->status->value }}
            </span>
        </h1>
        <div>
            <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-outline-primary">Editar</a>
            <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <h2 class="h5">Dados do contrato</h2>
            <dl class="row">
                <dt class="col-sm-5">Imóvel</dt>
                <dd class="col-sm-7">{{ $contrato->imovel->codigo }} — {{ $contrato->imovel->logradouro }}, {{ $contrato->imovel->numero }}</dd>

                <dt class="col-sm-5">Favorecido do repasse</dt>
                <dd class="col-sm-7">{{ $contrato->favorecido?->nome ?? 'Pessoa removida' }}</dd>

                <dt class="col-sm-5">Vigência</dt>
                <dd class="col-sm-7">
                    {{ $contrato->data_inicio->format('d/m/Y') }} até
                    {{ $contrato->data_fim?->format('d/m/Y') ?? 'em aberto' }}
                </dd>

                <dt class="col-sm-5">Dia de vencimento</dt>
                <dd class="col-sm-7">Todo dia {{ $contrato->dia_vencimento }}</dd>

                <dt class="col-sm-5">Garantia</dt>
                <dd class="col-sm-7">{{ ucfirst(strtolower(str_replace('_', ' ', $contrato->garantia->value))) }}</dd>

                <dt class="col-sm-5">Aluguel</dt>
                <dd class="col-sm-7">R$ {{ number_format($contrato->valor_aluguel, 2, ',', '.') }}</dd>

                <dt class="col-sm-5">Condomínio / IPTU / Seguro</dt>
                <dd class="col-sm-7">
                    R$ {{ number_format($contrato->valor_condominio, 2, ',', '.') }} /
                    R$ {{ number_format($contrato->valor_iptu, 2, ',', '.') }} /
                    R$ {{ number_format($contrato->valor_seguro, 2, ',', '.') }}
                </dd>

                <dt class="col-sm-5">Taxa de administração</dt>
                <dd class="col-sm-7">{{ $contrato->taxa_adm_percentual }}%</dd>
            </dl>
        </div>

        <div class="col-md-6">
            <h2 class="h5">Partes do contrato</h2>
            <ul class="list-group mb-3">
                @forelse ($contrato->partes as $parte)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $parte->pessoa?->nome ?? 'Pessoa removida' }}
                            <span class="badge bg-secondary">{{ str_replace('_', ' ', $parte->papel->value) }}</span>
                            @unless ($parte->assina_contrato)
                                <span class="badge bg-warning text-dark">não assina</span>
                            @endunless
                        </span>
                        <form action="{{ route('contrato-partes.destroy', $parte) }}" method="POST"
                              onsubmit="return confirm('Remover esta parte do contrato?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhuma parte cadastrada ainda.</li>
                @endforelse
            </ul>

            <form action="{{ route('contratos.partes.store', $contrato) }}" method="POST" class="row g-2 mb-4">
                @csrf
                <div class="col-md-5">
                    <select name="pessoa_id" class="form-select @error('pessoa_id') is-invalid @enderror" required>
                        <option value="">Selecione a pessoa...</option>
                        @foreach (\App\Models\Pessoa::orderBy('nome')->get(['id', 'nome']) as $pessoa)
                            <option value="{{ $pessoa->id }}">{{ $pessoa->nome }}</option>
                        @endforeach
                    </select>
                    @error('pessoa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <select name="papel" class="form-select" required>
                        @foreach (\App\Enums\PapelContrato::cases() as $opcao)
                            <option value="{{ $opcao->value }}">{{ str_replace('_', ' ', $opcao->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="assina_contrato" value="1" checked class="form-check-input" id="assina">
                        <label class="form-check-label" for="assina">Assina</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">+</button>
                </div>
            </form>

            <h2 class="h5 d-flex justify-content-between align-items-center">
                Faturas
                <a href="{{ route('faturas.create', ['contrato_id' => $contrato->id]) }}" class="btn btn-sm btn-outline-primary">Nova fatura</a>
            </h2>
            <ul class="list-group mb-4">
                @forelse ($contrato->faturas as $fatura)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('faturas.show', $fatura) }}">{{ $fatura->referencia->format('m/Y') }}</a>
                        <span class="badge bg-secondary">{{ str_replace('_', ' ', $fatura->status_pagamento->value) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhuma fatura gerada.</li>
                @endforelse
            </ul>

            @if (in_array($contrato->garantia, [\App\Enums\Garantia::SEGURO_FIANCA, \App\Enums\Garantia::TITULO_CAPITALIZACAO]))
                <h2 class="h5">Seguro / Fiança</h2>
                <ul class="list-group mb-3">
                    @forelse ($contrato->segurosFiancas as $seguro)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                {{ $seguro->seguradora }} — apólice {{ $seguro->numero_apolice ?? 's/n' }}
                                <span class="badge bg-secondary">{{ str_replace('_', ' ', $seguro->status->value) }}</span>
                                <div class="small text-muted">vence em {{ $seguro->data_vencimento_apolice->format('d/m/Y') }}</div>
                            </span>
                            <form action="{{ route('contrato-seguros.destroy', $seguro) }}" method="POST"
                                  onsubmit="return confirm('Remover esta apólice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Nenhuma apólice cadastrada.</li>
                    @endforelse
                </ul>

                <form action="{{ route('contratos.seguros.store', $contrato) }}" method="POST" class="row g-2">
                    @csrf
                    <input type="hidden" name="status" value="ATIVO">
                    <div class="col-md-3">
                        <select name="tipo" class="form-select" required>
                            @foreach (\App\Enums\TipoSeguro::cases() as $opcao)
                                <option value="{{ $opcao->value }}">{{ str_replace('_', ' ', $opcao->value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="seguradora" placeholder="Seguradora" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="numero_apolice" placeholder="Nº apólice" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="data_inicio" class="form-control" required title="Início da apólice">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="data_vencimento_apolice" class="form-control" required title="Vencimento da apólice">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-outline-primary">Adicionar apólice</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
