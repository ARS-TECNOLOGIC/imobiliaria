@php
    $fatura = $fatura ?? null;
    // $contratoSelecionado vem do botão "Nova fatura" do contrato (criação) OU
    // do próprio contrato da fatura (edição) — unificado aqui pra usar a mesma
    // lógica de sugestão de valores nos dois casos.
    $contratoSelecionado = $fatura?->contrato ?? $contratoSelecionado ?? null;
    $contratoPreSelecionado = $fatura?->contrato_id ?? $contratoSelecionado?->id;
    $baseParaValores = $fatura ?? $contratoSelecionado ?? null;

    // Condomínio só é sugerido automaticamente se o imóvel de fato tiver condomínio;
    // caso contrário, sugerimos 0 em vez de copiar às cegas o valor do contrato.
    $valorCondominioSugerido = ($contratoSelecionado && $contratoSelecionado->imovel->possui_condominio)
        ? $contratoSelecionado->valor_condominio
        : 0;

    // Dados que o JS da tela precisa pra recalcular multa/total/valor pago em
    // tempo real, sem esperar o usuário salvar: percentuais de cada contrato
    // e as datas de feriado (pra respeitar a regra de dia útil no preview).
    $contratosInfo = $contratos->mapWithKeys(fn ($c) => [
        $c->id => ['multa' => (float) $c->multa_percentual],
    ]);
    $feriados = \App\Models\Feriado::pluck('data')->map(fn ($d) => $d->format('Y-m-d'));
@endphp

<div class="row g-3" id="form-fatura">
    <div class="col-md-6">
        <label class="form-label">Contrato *</label>
        <select name="contrato_id" id="contrato_id" class="form-select @error('contrato_id') is-invalid @enderror"
                {{ $fatura ? 'disabled' : '' }}>
            <option value="">Selecione...</option>
            @foreach ($contratos as $contrato)
                <option value="{{ $contrato->id }}" @selected(old('contrato_id', $contratoPreSelecionado) == $contrato->id)>
                    Contrato #{{ $contrato->id }} — {{ $contrato->imovel->codigo }}
                </option>
            @endforeach
        </select>
        @if ($fatura)
            {{-- Contrato não pode ser trocado depois de criada a fatura; reenviamos o valor via hidden. --}}
            <input type="hidden" name="contrato_id" id="contrato_id_hidden" value="{{ $fatura->contrato_id }}">
            <div class="form-text">O contrato não pode ser alterado após a fatura ser criada.</div>
        @endif
        @error('contrato_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Referência (mês) *</label>
        <input type="month" name="referencia_mes"
               value="{{ old('referencia_mes', $fatura?->referencia?->format('Y-m')) }}"
               class="form-control @error('referencia') is-invalid @enderror"
               onchange="document.getElementById('referencia_hidden').value = this.value + '-01'">
        <input type="hidden" id="referencia_hidden" name="referencia"
               value="{{ old('referencia', $fatura?->referencia?->format('Y-m-d')) }}">
        @error('referencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Data de vencimento *</label>
        <input type="date" name="data_vencimento" id="data_vencimento"
               value="{{ old('data_vencimento', $fatura?->data_vencimento?->format('Y-m-d')) }}"
               class="form-control @error('data_vencimento') is-invalid @enderror">
        @error('data_vencimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12"><hr><h2 class="h6">Valores</h2></div>

    <div class="col-md-2">
        <label class="form-label">Aluguel *</label>
        <input type="number" step="0.01" name="valor_aluguel" id="valor_aluguel"
               value="{{ old('valor_aluguel', $baseParaValores?->valor_aluguel) }}"
               class="form-control @error('valor_aluguel') is-invalid @enderror">
        @error('valor_aluguel') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Condomínio</label>
        <input type="number" step="0.01" name="valor_condominio" id="valor_condominio"
               value="{{ old('valor_condominio', $fatura?->valor_condominio ?? $valorCondominioSugerido) }}" class="form-control">
        @if ($contratoSelecionado && ! $contratoSelecionado->imovel->possui_condominio)
            <div class="form-text">Este imóvel não tem condomínio cadastrado.</div>
        @endif
    </div>

    <div class="col-md-2">
        <label class="form-label">IPTU</label>
        <input type="number" step="0.01" name="valor_iptu" id="valor_iptu"
               value="{{ old('valor_iptu', $fatura?->valor_iptu ?? 0) }}" class="form-control">
        <div class="form-text">Não é pré-preenchido: confirme se este mês tem cobrança de IPTU.</div>
    </div>

    <div class="col-md-2">
        <label class="form-label">Parcela IPTU</label>
        <input type="text" name="parcela_iptu" value="{{ old('parcela_iptu', $fatura?->parcela_iptu) }}" class="form-control" placeholder="Ex: 03/10">
    </div>

    <div class="col-md-2">
        <label class="form-label">Seguro</label>
        <input type="number" step="0.01" name="valor_seguro" id="valor_seguro"
               value="{{ old('valor_seguro', $baseParaValores?->valor_seguro ?? 0) }}" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Multa/juros</label>
        <input type="text" id="preview_multa" value="{{ number_format($fatura?->valor_multa_juros ?? 0, 2, ',', '.') }}" class="form-control" disabled>
        <div class="form-check mt-1">
            <input type="checkbox" name="isento_multa" value="1" id="isento_multa" class="form-check-input"
                   {{ old('isento_multa', $fatura?->isento_multa) ? 'checked' : '' }}>
            <label class="form-check-label small" for="isento_multa">Isentar multa</label>
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label">Taxa extra</label>
        <input type="number" step="0.01" name="valor_taxa_extra" id="valor_taxa_extra" value="{{ old('valor_taxa_extra', $fatura?->valor_taxa_extra ?? 0) }}" class="form-control">
    </div>
    <div class="col-md-5">
        <label class="form-label">Descrição da taxa extra</label>
        <input type="text" name="descricao_taxa_extra" value="{{ old('descricao_taxa_extra', $fatura?->descricao_taxa_extra) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Desconto</label>
        <input type="number" step="0.01" name="valor_desconto" id="valor_desconto" value="{{ old('valor_desconto', $fatura?->valor_desconto ?? 0) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Descrição do desconto</label>
        <input type="text" name="descricao_desconto" value="{{ old('descricao_desconto', $fatura?->descricao_desconto) }}" class="form-control">
    </div>

    <div class="col-md-5 d-flex align-items-end">
        <div class="alert alert-secondary mb-0 py-2 w-100 text-center">
            <div class="small text-muted">Valor total da fatura</div>
            <strong id="preview_total_fatura" class="fs-5">R$ 0,00</strong>
        </div>
    </div>

    <div class="col-12"><hr><h2 class="h6">Pagamento</h2></div>

    <div class="col-md-3">
        <label class="form-label">Status de pagamento *</label>
        <select name="status_pagamento" id="status_pagamento" class="form-select @error('status_pagamento') is-invalid @enderror">
            @foreach (\App\Enums\StatusPagamento::cases() as $opcao)
                <option value="{{ $opcao->value }}"
                    @selected(old('status_pagamento', $fatura?->status_pagamento?->value ?? 'PENDENTE') === $opcao->value)>
                    {{ str_replace('_', ' ', $opcao->value) }}
                </option>
            @endforeach
        </select>
        @error('status_pagamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Valor pago</label>
        <input type="text" id="preview_valor_pago" value="{{ $fatura?->valor_pago !== null ? number_format($fatura->valor_pago, 2, ',', '.') : '—' }}" class="form-control" disabled>
        <div class="form-text">Total da fatura, se o status for Pago.</div>
    </div>

    <div class="col-md-2">
        <label class="form-label">Dias de atraso</label>
        <input type="text" id="preview_dias_atraso" value="{{ $fatura?->dias_atraso ?? 0 }}" class="form-control" disabled>
        <div class="form-text">Considera dia útil (fim de semana/feriado).</div>
    </div>

    <div class="col-md-2">
        <label class="form-label">Data de recebimento</label>
        <input type="date" name="data_recebimento" id="data_recebimento" value="{{ old('data_recebimento', $fatura?->data_recebimento?->format('Y-m-d')) }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Banco recebedor</label>
        <input type="text" name="banco_recebedor" value="{{ old('banco_recebedor', $fatura?->banco_recebedor) }}" class="form-control">
    </div>
</div>

<script>
(function () {
    // Espelha, no navegador, o mesmo cálculo que o FaturaController faz no
    // servidor — serve só de PRÉVIA em tempo real. O valor que realmente
    // grava no banco é sempre recalculado pelo backend ao salvar.
    const contratosInfo = @json($contratosInfo);
    const feriados = new Set(@json($feriados));

    const $ = (id) => document.getElementById(id);

    function parseDataLocal(str) {
        if (!str) return null;
        const [ano, mes, dia] = str.split('-').map(Number);
        return new Date(ano, mes - 1, dia);
    }

    function formatarYMD(data) {
        const ano = data.getFullYear();
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');
        return `${ano}-${mes}-${dia}`;
    }

    function ehDiaUtil(data) {
        const diaSemana = data.getDay();
        if (diaSemana === 0 || diaSemana === 6) return false;
        return !feriados.has(formatarYMD(data));
    }

    function proximoDiaUtil(data) {
        const d = new Date(data.getTime());
        while (!ehDiaUtil(d)) d.setDate(d.getDate() + 1);
        return d;
    }

    function diffDias(a, b) {
        return Math.round((b.getTime() - a.getTime()) / 86400000);
    }

    function numero(valor) {
        return parseFloat(valor) || 0;
    }

    function formatarBRL(valor) {
        return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function contratoIdAtual() {
        const hidden = $('contrato_id_hidden');
        if (hidden) return hidden.value;
        return $('contrato_id').value;
    }

    function recalcular() {
        const aluguel = numero($('valor_aluguel').value);
        const condominio = numero($('valor_condominio').value);
        const iptu = numero($('valor_iptu').value);
        const seguro = numero($('valor_seguro').value);
        const taxaExtra = numero($('valor_taxa_extra').value);
        const desconto = numero($('valor_desconto').value);
        const isento = $('isento_multa').checked;
        const status = $('status_pagamento').value;
        const base = aluguel + condominio + iptu + seguro;

        let diasAtraso = 0;
        const vencimentoStr = $('data_vencimento').value;
        const contratoId = contratoIdAtual();

        if (vencimentoStr) {
            const vencimentoEfetivo = proximoDiaUtil(parseDataLocal(vencimentoStr));
            let referencia;

            if (status === 'PAGO' || status === 'PAGO_COM_ATRASO') {
                const recebStr = $('data_recebimento').value;
                referencia = recebStr ? parseDataLocal(recebStr) : vencimentoEfetivo;
            } else if (status === 'ATRASADO') {
                referencia = new Date();
                referencia.setHours(0, 0, 0, 0);
            } else {
                referencia = vencimentoEfetivo;
            }

            diasAtraso = Math.max(0, diffDias(vencimentoEfetivo, referencia));
        }

        const percentualMulta = contratosInfo[contratoId]?.multa ?? 0;
        const multa = (diasAtraso > 0 && !isento) ? Math.round(base * (percentualMulta / 100) * 100) / 100 : 0;

        const totalFatura = base + taxaExtra - desconto + multa;

        $('preview_dias_atraso').value = diasAtraso;
        $('preview_multa').value = formatarBRL(multa);
        $('preview_total_fatura').textContent = formatarBRL(totalFatura);
        $('preview_valor_pago').value = (status === 'PAGO' || status === 'PAGO_COM_ATRASO')
            ? formatarBRL(Math.round(totalFatura * 100) / 100)
            : '—';
    }

    const form = $('form-fatura');
    form.addEventListener('input', recalcular);
    form.addEventListener('change', recalcular);
    recalcular();
})();
</script>
