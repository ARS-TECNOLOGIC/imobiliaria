{{-- Partial: Campo de configuração individual --}}
<div class="mb-3 pb-3 border-bottom last:border-0 last:pb-0">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <label class="form-label fw-medium mb-0" for="config-{{ $config->id }}">
            {{ $config->chave }}
            @if ($config->sistema)
                <span class="badge bg-primary ms-1" title="Configuração de sistema"><i class="fa-solid fa-shield-halved"></i></span>
            @endif
        </label>
    </div>
    @if ($config->descricao)
        <div class="text-muted small mb-2">{{ $config->descricao }}</div>
    @endif

    @php
        $valorExibicao = match($config->tipo) {
            'boolean' => $config->valor ? 'Sim' : 'Não',
            'json', 'array' => is_array($config->valor) ? json_encode($config->valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $config->valor,
            default => $config->valor,
        };
        $tipoInput = match($config->tipo) {
            'boolean' => 'checkbox',
            'integer' => 'number',
            'decimal' => 'number',
            default => 'text',
        };
        $step = $config->tipo === 'decimal' ? 'step="0.01"' : '';
    @endphp

    <div class="form-floating">
        @if ($config->tipo === 'boolean')
            <div class="form-check form-switch">
                @if (! $config->sistema)
                    <input type="hidden" name="config[{{ $config->id }}]" value="0">
                @endif
                <input class="form-check-input" type="checkbox" role="switch"
                    id="config-{{ $config->id }}"
                    name="config[{{ $config->id }}]"
                    value="1"
                    {{ $config->valor ? 'checked' : '' }}
                    {{ $config->sistema ? 'disabled' : '' }}>
                <label class="form-check-label" for="config-{{ $config->id }}">{{ $valorExibicao }}</label>
            </div>
        @elseif ($config->tipo === 'array')
            <textarea class="form-control" id="config-{{ $config->id }}" name="config[{{ $config->id }}]" rows="3" {{ $config->sistema ? 'readonly' : '' }} placeholder="Um item por linha">{{ is_array($config->valor) ? implode("\n", $config->valor) : $config->valor }}</textarea>
            <label for="config-{{ $config->id }}">Valores (um por linha)</label>
        @else
            <input type="{{ $tipoInput }}" class="form-control" id="config-{{ $config->id }}" name="config[{{ $config->id }}]" value="{{ $valorExibicao }}" {{ $step }} {{ $config->sistema ? 'readonly' : '' }}>
            <label for="config-{{ $config->id }}">Valor</label>
        @endif
    </div>

    @if ($config->sistema)
        <div class="form-text text-muted">
            <i class="fa-solid fa-circle-info me-1"></i>Configuração de sistema - apenas visualização
        </div>
    @endif

    @if (! $config->sistema)
        <input type="hidden" name="tipos[{{ $config->id }}]" value="{{ $config->tipo }}">
    @endif
</div>