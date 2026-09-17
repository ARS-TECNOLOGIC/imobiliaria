<?php

namespace App\Http\Requests;

use App\Enums\TipoImovel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imovelId = $this->route('imovel')?->id;

        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('imoveis', 'codigo')->ignore($imovelId)],
            'locador_id' => ['required', 'exists:pessoas,id'],
            'tipo' => ['required', Rule::enum(TipoImovel::class)],
            'cep' => ['nullable', 'string', 'max:10'],
            'logradouro' => ['required', 'string', 'max:150'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:50'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'uf' => ['required', 'string', 'size:2'],
            'possui_condominio' => ['nullable', 'boolean'],
            'nome_condominio' => ['nullable', 'string', 'max:150', 'required_if:possui_condominio,1'],
            'administradora_condominio' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'locador_id' => 'locador',
            'nome_condominio' => 'nome do condomínio',
            'administradora_condominio' => 'administradora do condomínio',
        ];
    }
}
