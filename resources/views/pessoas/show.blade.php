@extends('layouts.app')

@section('titulo', $pessoa->nome)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $pessoa->nome }}</h1>
        <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <h2 class="h5">Dados pessoais</h2>
            <dl class="row">
                <dt class="col-sm-4">CPF/CNPJ</dt>
                <dd class="col-sm-8">{{ $pessoa->cpf_cnpj }}</dd>

                <dt class="col-sm-4">Estado civil</dt>
                <dd class="col-sm-8">{{ $pessoa->estado_civil->value }}</dd>

                <dt class="col-sm-4">E-mail</dt>
                <dd class="col-sm-8">{{ $pessoa->email ?? '—' }}</dd>

                <dt class="col-sm-4">Telefone / Celular</dt>
                <dd class="col-sm-8">{{ $pessoa->telefone ?? '—' }} / {{ $pessoa->celular ?? '—' }}</dd>

                <dt class="col-sm-4">Endereço</dt>
                <dd class="col-sm-8">
                    {{ $pessoa->logradouro }}, {{ $pessoa->numero }} — {{ $pessoa->bairro }},
                    {{ $pessoa->cidade }}/{{ $pessoa->uf }}
                </dd>
            </dl>

            @if ($pessoa->relacionamentos->isNotEmpty())
                <hr>
                <h2 class="h5">Vínculo conjugal</h2>
                <ul class="list-group">
                    @foreach ($pessoa->relacionamentos as $rel)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                {{ $rel->conjuge?->nome ?? 'Pessoa removida' }}
                                <span class="badge bg-secondary">
                                    {{ strtolower(str_replace('_', ' ', $rel->tipo_vinculo->value)) }}
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="col-md-6">
            <h2 class="h5">Imóveis (como locador)</h2>
            <ul class="list-group mb-4">
                @forelse ($pessoa->imoveis as $imovel)
                    <li class="list-group-item">{{ $imovel->codigo }} — {{ $imovel->logradouro }}, {{ $imovel->numero }}</li>
                @empty
                    <li class="list-group-item text-muted">Nenhum imóvel.</li>
                @endforelse
            </ul>

            <h2 class="h5">Contratos (em qualquer papel)</h2>
            <ul class="list-group">
                @forelse ($pessoa->contratos as $contrato)
                    <li class="list-group-item">
                        Contrato #{{ $contrato->id }} — {{ $contrato->pivot->papel }}
                        <span class="badge bg-secondary">{{ $contrato->status->value }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhum contrato.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
