@php
    $imovel = $imovel ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Código *</label>
        <input type="text" name="codigo" value="{{ old('codigo', $imovel?->codigo) }}"
               class="form-control @error('codigo') is-invalid @enderror">
        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-5">
        <label class="form-label">Locador *</label>
        <select name="locador_id" class="form-select @error('locador_id') is-invalid @enderror">
            <option value="">Selecione...</option>
            @foreach ($locadores as $pessoa)
                <option value="{{ $pessoa->id }}"
                    @selected(old('locador_id', $imovel?->locador_id) == $pessoa->id)>
                    {{ $pessoa->nome }}
                </option>
            @endforeach
        </select>
        @error('locador_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tipo *</label>
        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror">
            @foreach (\App\Enums\TipoImovel::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('tipo', $imovel?->tipo?->value) === $opcao->value)>
                    {{ ucfirst(strtolower($opcao->value)) }}
                </option>
            @endforeach
        </select>
        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12"><hr><h2 class="h6">Endereço</h2></div>

    <div class="col-md-2">
        <label class="form-label">CEP</label>
        <input type="text" name="cep" value="{{ old('cep', $imovel?->cep) }}" class="form-control">
    </div>

    <div class="col-md-5">
        <label class="form-label">Logradouro *</label>
        <input type="text" name="logradouro" value="{{ old('logradouro', $imovel?->logradouro) }}"
               class="form-control @error('logradouro') is-invalid @enderror">
        @error('logradouro') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Número *</label>
        <input type="text" name="numero" value="{{ old('numero', $imovel?->numero) }}"
               class="form-control @error('numero') is-invalid @enderror">
        @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Complemento</label>
        <input type="text" name="complemento" value="{{ old('complemento', $imovel?->complemento) }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label">Bairro</label>
        <input type="text" name="bairro" value="{{ old('bairro', $imovel?->bairro) }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Cidade *</label>
        <input type="text" name="cidade" value="{{ old('cidade', $imovel?->cidade ?? 'Bauru') }}"
               class="form-control @error('cidade') is-invalid @enderror">
        @error('cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">UF *</label>
        <input type="text" name="uf" maxlength="2" value="{{ old('uf', $imovel?->uf ?? 'SP') }}"
               class="form-control @error('uf') is-invalid @enderror">
        @error('uf') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12"><hr><h2 class="h6">Condomínio</h2></div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="possui_condominio" value="1" id="possui_condominio"
                   class="form-check-input" {{ old('possui_condominio', $imovel?->possui_condominio) ? 'checked' : '' }}>
            <label class="form-check-label" for="possui_condominio">Possui condomínio</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nome do condomínio</label>
        <input type="text" name="nome_condominio" value="{{ old('nome_condominio', $imovel?->nome_condominio) }}"
               class="form-control @error('nome_condominio') is-invalid @enderror">
        @error('nome_condominio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Administradora do condomínio</label>
        <input type="text" name="administradora_condominio" value="{{ old('administradora_condominio', $imovel?->administradora_condominio) }}" class="form-control">
    </div>
</div>
