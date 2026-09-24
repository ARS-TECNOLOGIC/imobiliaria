<?php

use App\Models\Configuracao;

if (! function_exists('configuracao')) {
    /**
     * Obtém uma configuração do sistema.
     *
     * @param  string  $chave  Chave da configuração
     * @param  mixed  $default  Valor padrão se não existir
     */
    function configuracao(string $chave, mixed $default = null): mixed
    {
        return Configuracao::where('chave', $chave)->value('valor') ?? $default;
    }
}
