@php
    $contrato = $contrato ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Imóvel *</label>
        <select name="imovel_id" class="form-select @error('imovel_id') is-invalid @enderror">
            <option value="">Selecione...</option>
            @foreach ($imoveis as $imovel)
                <option value="{{ $imovel->id }}"
                    @selected(old('imovel_id', $contrato?->imovel_id) == $imovel->id)>
                    {{ $imovel->codigo }} — {{ $imovel->logradouro }}, {{ $imovel->numero }}
                </option>
            @endforeach
        </select>
        @error('imovel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Favorecido do repasse *</label>
        <select name="favorecido_id" class="form-select @error('favorecido_id') is-invalid @enderror">
            <option value="">Selecione...</option>
            @foreach ($pessoas as $pessoa)
                <option value="{{ $pessoa->id }}"
                    @selected(old('favorecido_id', $contrato?->favorecido_id) == $pessoa->id)>
                    {{ $pessoa->nome }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Normalmente é o próprio locador do imóvel selecionado acima.</div>
        @error('favorecido_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Data de início *</label>
        <input type="date" name="data_inicio" value="{{ old('data_inicio', $contrato?->data_inicio?->format('Y-m-d')) }}"
               class="form-control @error('data_inicio') is-invalid @enderror">
        @error('data_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Data de fim</label>
        <input type="date" name="data_fim" value="{{ old('data_fim', $contrato?->data_fim?->format('Y-m-d')) }}"
               class="form-control @error('data_fim') is-invalid @enderror">
        @error('data_fim') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Dia de vencimento *</label>
        <input type="number" min="1" max="28" name="dia_vencimento"
               value="{{ old('dia_vencimento', $contrato?->dia_vencimento) }}"
               class="form-control @error('dia_vencimento') is-invalid @enderror">
        @error('dia_vencimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Status *</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach (\App\Enums\StatusContrato::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('status', $contrato?->status?->value ?? 'ATIVO') === $opcao->value)>
                    {{ $opcao->value }}
                </option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12"><hr><h2 class="h6">Garantia</h2></div>

    <div class="col-md-4">
        <label class="form-label">Tipo de garantia *</label>
        <select name="garantia" class="form-select @error('garantia') is-invalid @enderror">
            @foreach (\App\Enums\Garantia::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('garantia', $contrato?->garantia?->value) === $opcao->value)>
                    {{ ucfirst(strtolower(str_replace('_', ' ', $opcao->value))) }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Se for fiador, adicione a pessoa depois na tela do contrato.</div>
        @error('garantia') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12"><hr><h2 class="h6">Valores</h2></div>

    <div class="col-md-2">
        <label class="form-label">Aluguel *</label>
        <input type="number" step="0.01" name="valor_aluguel" value="{{ old('valor_aluguel', $contrato?->valor_aluguel) }}"
               class="form-control @error('valor_aluguel') is-invalid @enderror">
        @error('valor_aluguel') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Condomínio</label>
        <input type="number" step="0.01" name="valor_condominio" value="{{ old('valor_condominio', $contrato?->valor_condominio ?? 0) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">IPTU</label>
        <input type="number" step="0.01" name="valor_iptu" value="{{ old('valor_iptu', $contrato?->valor_iptu ?? 0) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Parcela IPTU</label>
        <input type="text" name="parcela_iptu" value="{{ old('parcela_iptu', $contrato?->parcela_iptu) }}" class="form-control" placeholder="Ex: 03/10">
    </div>

    <div class="col-md-2">
        <label class="form-label">Seguro</label>
        <input type="number" step="0.01" name="valor_seguro" value="{{ old('valor_seguro', $contrato?->valor_seguro ?? 0) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Taxa adm. (%) *</label>
        <input type="number" step="0.01" name="taxa_adm_percentual"
               value="{{ old('taxa_adm_percentual', $contrato?->taxa_adm_percentual ?? 8) }}"
               class="form-control @error('taxa_adm_percentual') is-invalid @enderror">
        @error('taxa_adm_percentual') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Multa por atraso (%) *</label>
        <input type="number" step="0.01" name="multa_percentual"
               value="{{ old('multa_percentual', $contrato?->multa_percentual ?? 10) }}"
               class="form-control @error('multa_percentual') is-invalid @enderror">
        <div class="form-text">Aplicada automaticamente sobre o valor total quando a fatura atrasar.</div>
        @error('multa_percentual') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
