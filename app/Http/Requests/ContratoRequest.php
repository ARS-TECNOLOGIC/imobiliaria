<?php

namespace App\Http\Requests;

use App\Enums\Garantia;
use App\Enums\StatusContrato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'imovel_id' => ['required', 'exists:imoveis,id'],
            'favorecido_id' => ['required', 'exists:pessoas,id'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'dia_vencimento' => ['required', 'integer', 'min:1', 'max:28'],
            'garantia' => ['required', Rule::enum(Garantia::class)],
            'valor_aluguel' => ['required', 'numeric', 'min:0'],
            'valor_condominio' => ['nullable', 'numeric', 'min:0'],
            'valor_iptu' => ['nullable', 'numeric', 'min:0'],
            'parcela_iptu' => ['nullable', 'string', 'max:20'],
            'valor_seguro' => ['nullable', 'numeric', 'min:0'],
            'taxa_adm_percentual' => ['required', 'numeric', 'min:0', 'max:100'],
            'multa_percentual' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', Rule::enum(StatusContrato::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'imovel_id' => 'imóvel',
            'favorecido_id' => 'favorecido do repasse',
            'data_inicio' => 'data de início',
            'data_fim' => 'data de fim',
            'dia_vencimento' => 'dia de vencimento',
            'valor_aluguel' => 'valor do aluguel',
            'valor_condominio' => 'valor do condomínio',
            'valor_iptu' => 'valor do IPTU',
            'parcela_iptu' => 'parcela do IPTU',
            'valor_seguro' => 'valor do seguro',
            'taxa_adm_percentual' => 'taxa de administração',
            'multa_percentual' => 'percentual de multa por atraso',
        ];
    }
}
