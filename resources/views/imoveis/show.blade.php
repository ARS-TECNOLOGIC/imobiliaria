@extends('layouts.app')

@section('titulo', $imovel->codigo)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $imovel->codigo }} — {{ $imovel->logradouro }}, {{ $imovel->numero }}</h1>
        <a href="{{ route('imoveis.index') }}" class="btn btn-outline-secondary">Voltar</a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <h2 class="h5">Dados do imóvel</h2>
            <dl class="row">
                <dt class="col-sm-4">Locador</dt>
                <dd class="col-sm-8">{{ $imovel->locador?->nome ?? 'Pessoa Removida' }}</dd>

                <dt class="col-sm-4">Tipo</dt>
                <dd class="col-sm-8">{{ ucfirst(strtolower($imovel->tipo->value)) }}</dd>

                <dt class="col-sm-4">Endereço</dt>
                <dd class="col-sm-8">
                    {{ $imovel->logradouro }}, {{ $imovel->numero }} — {{ $imovel->bairro }},
                    {{ $imovel->cidade }}/{{ $imovel->uf }}
                </dd>

                <dt class="col-sm-4">Condomínio</dt>
                <dd class="col-sm-8">
                    @if ($imovel->possui_condominio)
                        {{ $imovel->nome_condominio }} ({{ $imovel->administradora_condominio ?? 'sem administradora' }})
                    @else
                        Não possui
                    @endif
                </dd>
            </dl>

            <h2 class="h5">Corretores na carteira</h2>
            <ul class="list-group mb-4">
                @forelse ($imovel->corretores as $corretor)
                    <li class="list-group-item">
                        {{ $corretor->pessoa->nome }}
                        <span class="badge bg-secondary">{{ $corretor->pivot->ativo ? 'Ativo' : 'Inativo' }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhum corretor atribuído.</li>
                @endforelse
            </ul>
        </div>

        <div class="col-md-6">
            <h2 class="h5">Contratos</h2>
            <ul class="list-group mb-4">
                @forelse ($imovel->contratos as $contrato)
                    <li class="list-group-item">
                        Contrato #{{ $contrato->id }}
                        <span class="badge bg-secondary">{{ $contrato->status->value }}</span>
                        — {{ $contrato->faturas->count() }} fatura(s)
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhum contrato.</li>
                @endforelse
            </ul>

            @if ($imovel->possui_condominio)
                <h2 class="h5">Serviços do condomínio</h2>
                <ul class="list-group mb-3">
                    @forelse ($imovel->servicosCondominio as $servico)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                {{ ucfirst(strtolower($servico->servico->value)) }}
                                @if ($servico->descricao_outro) ({{ $servico->descricao_outro }}) @endif
                                — {{ ucfirst(strtolower(str_replace('_', ' ', $servico->tipo_cobranca->value))) }}
                            </span>
                            <form action="{{ route('imovel-servicos.destroy', $servico) }}" method="POST"
                                  onsubmit="return confirm('Remover este serviço?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Nenhum serviço cadastrado.</li>
                    @endforelse
                </ul>

                <form action="{{ route('imoveis.servicos.store', $imovel) }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-md-3">
                        <select name="servico" class="form-select" required>
                            @foreach (\App\Enums\ServicoCondominio::cases() as $opcao)
                                <option value="{{ $opcao->value }}">{{ ucfirst(strtolower($opcao->value)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="tipo_cobranca" class="form-select" required>
                            @foreach (\App\Enums\TipoCobranca::cases() as $opcao)
                                <option value="{{ $opcao->value }}">{{ ucfirst(strtolower(str_replace('_', ' ', $opcao->value))) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="observacao" placeholder="Observação (opcional)" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Adicionar</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
