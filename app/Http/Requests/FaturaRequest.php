<?php

namespace App\Http\Requests;

use App\Enums\StatusPagamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrato_id' => ['required', 'exists:contratos,id'],
            'referencia' => [
                'required', 'date',
                Rule::unique('faturas')->where(fn ($query) => $query
                    ->where('contrato_id', $this->input('contrato_id'))
                )->ignore($this->route('fatura')),
            ],
            'data_vencimento' => ['nullable', 'date'],
            'valor_aluguel' => ['required', 'numeric', 'min:0'],
            'valor_condominio' => ['nullable', 'numeric', 'min:0'],
            'valor_iptu' => ['nullable', 'numeric', 'min:0'],
            'parcela_iptu' => ['nullable', 'string', 'max:20'],
            'valor_seguro' => ['nullable', 'numeric', 'min:0'],
            'valor_taxa_extra' => ['nullable', 'numeric', 'min:0'],
            'descricao_taxa_extra' => ['nullable', 'string', 'max:255'],
            'valor_desconto' => ['nullable', 'numeric', 'min:0'],
            'descricao_desconto' => ['nullable', 'string', 'max:255'],
            'valor_multa_juros' => ['nullable', 'numeric', 'min:0'],
            'valor_pago' => ['nullable', 'numeric', 'min:0'],
            'dias_atraso' => ['nullable', 'integer', 'min:0'],
            'isento_multa' => ['nullable', 'boolean'],
            'status_pagamento' => ['required', Rule::enum(StatusPagamento::class)],
            'data_recebimento' => ['nullable', 'date'],
            'banco_recebedor' => ['nullable', 'string', 'max:100'],
            'descricao_boleto' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'contrato_id' => 'contrato',
            'referencia' => 'referência',
            'valor_aluguel' => 'valor do aluguel',
            'valor_condominio' => 'valor do condomínio',
            'valor_iptu' => 'valor do IPTU',
            'valor_seguro' => 'valor do seguro',
            'valor_taxa_extra' => 'taxa extra',
            'valor_desconto' => 'desconto',
            'valor_multa_juros' => 'multa/juros',
            'valor_pago' => 'valor pago',
            'dias_atraso' => 'dias de atraso',
            'status_pagamento' => 'status de pagamento',
            'data_recebimento' => 'data de recebimento',
            'banco_recebedor' => 'banco recebedor',
        ];
    }
}
