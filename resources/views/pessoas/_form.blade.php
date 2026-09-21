@php
    // $pessoa existe (e vem preenchida) na tela de edição; na de criação, é null.
    $pessoa = $pessoa ?? null;
    $estadosVinculo = [\App\Enums\EstadoCivil::CASADO, \App\Enums\EstadoCivil::UNIAO_ESTAVEL];
    $estadoAtual = old('estado_civil', $pessoa?->estado_civil?->value);
    $mostrarVinculo = in_array($estadoAtual, array_column($estadosVinculo, 'value'));
    $vinculoExistente = $pessoa?->relacionamentos?->first();
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nome *</label>
        <input type="text" name="nome" value="{{ old('nome', $pessoa?->nome) }}"
               class="form-control @error('nome') is-invalid @enderror">
        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">CPF/CNPJ *</label>
        <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $pessoa?->cpf_cnpj) }}"
               class="form-control @error('cpf_cnpj') is-invalid @enderror">
        @error('cpf_cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">RG</label>
        <input type="text" name="rg" value="{{ old('rg', $pessoa?->rg) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Data de nascimento</label>
        <input type="date" name="data_nascimento"
               value="{{ old('data_nascimento', $pessoa?->data_nascimento?->format('Y-m-d')) }}"
               class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Estado civil *</label>
        <select name="estado_civil" class="form-select @error('estado_civil') is-invalid @enderror" id="estado-civil">
            @foreach (\App\Enums\EstadoCivil::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('estado_civil', $pessoa?->estado_civil?->value) === $opcao->value)>
                    {{ ucfirst(strtolower(str_replace('_', ' ', $opcao->value))) }}
                </option>
            @endforeach
        </select>
        @error('estado_civil') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Regime de bens</label>
        <select name="regime_bens" class="form-select">
            <option value="">—</option>
            @foreach (\App\Enums\RegimeBens::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('regime_bens', $pessoa?->regime_bens?->value) === $opcao->value)>
                    {{ ucfirst(strtolower(str_replace('_', ' ', $opcao->value))) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" value="{{ old('email', $pessoa?->email) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Telefone</label>
        <input type="text" name="telefone" value="{{ old('telefone', $pessoa?->telefone) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Celular</label>
        <input type="text" name="celular" value="{{ old('celular', $pessoa?->celular) }}" class="form-control">
    </div>

    {{-- Seção de vínculo conjugal --}}
    <div id="secao-vinculo" class="col-12" style="{{ $mostrarVinculo ? '' : 'display:none' }}">
        <hr>
        <h2 class="h6">
            <i class="fa-solid fa-link ds-section-icon"></i>
            Vincular cônjuge / companheiro(a)
        </h2>

        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Cônjuge</label>
                <select name="conjuge_id" id="conjuge-select" class="form-select">
                    <option value="">— Selecione uma pessoa —</option>
                    @if ($vinculoExistente && $vinculoExistente->conjuge)
                        <option value="{{ $vinculoExistente->conjuge_id }}" selected>
                            {{ $vinculoExistente->conjuge->nome }} ({{ $vinculoExistente->conjuge->cpf_cnpj }})
                        </option>
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNovaPessoa">
                    <i class="fa-solid fa-plus me-1"></i> Cadastrar nova pessoa
                </button>
            </div>
        </div>
    </div>

    <div class="col-12"><hr><h2 class="h6">Endereço</h2></div>

    <div class="col-md-2">
        <label class="form-label">CEP</label>
        <input type="text" name="cep" value="{{ old('cep', $pessoa?->cep) }}" class="form-control">
    </div>

    <div class="col-md-5">
        <label class="form-label">Logradouro</label>
        <input type="text" name="logradouro" value="{{ old('logradouro', $pessoa?->logradouro) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Número</label>
        <input type="text" name="numero" value="{{ old('numero', $pessoa?->numero) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Complemento</label>
        <input type="text" name="complemento" value="{{ old('complemento', $pessoa?->complemento) }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label">Bairro</label>
        <input type="text" name="bairro" value="{{ old('bairro', $pessoa?->bairro) }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Cidade</label>
        <input type="text" name="cidade" value="{{ old('cidade', $pessoa?->cidade) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">UF</label>
        <input type="text" name="uf" maxlength="2" value="{{ old('uf', $pessoa?->uf) }}" class="form-control">
    </div>

    <div class="col-12"><hr><h2 class="h6">Dados bancários / PIX</h2></div>

    <div class="col-md-3">
        <label class="form-label">Tipo de chave PIX</label>
        <input type="text" name="tipo_chave_pix" value="{{ old('tipo_chave_pix', $pessoa?->tipo_chave_pix) }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label">Chave PIX</label>
        <input type="text" name="chave_pix" value="{{ old('chave_pix', $pessoa?->chave_pix) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Banco</label>
        <input type="text" name="banco" value="{{ old('banco', $pessoa?->banco) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Agência</label>
        <input type="text" name="agencia" value="{{ old('agencia', $pessoa?->agencia) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Conta</label>
        <input type="text" name="conta" value="{{ old('conta', $pessoa?->conta) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Tipo de conta *</label>
        <select name="tipo_conta" class="form-select @error('tipo_conta') is-invalid @enderror">
            @foreach (\App\Enums\TipoConta::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('tipo_conta', $pessoa?->tipo_conta?->value) === $opcao->value)>
                    {{ ucfirst($opcao->value) }}
                </option>
            @endforeach
        </select>
        @error('tipo_conta') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

