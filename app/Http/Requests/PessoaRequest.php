<?php

namespace App\Http\Requests;

use App\Enums\EstadoCivil;
use App\Enums\RegimeBens;
use App\Enums\TipoConta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PessoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ao editar, a pessoa atual é ignorada na checagem de CPF/CNPJ único
        // ($this->pessoa vem do route model binding: Route::resource('pessoas', ...)).
        $pessoaId = $this->route('pessoa')?->id;

        return [
            'nome' => ['required', 'string', 'max:150'],
            'cpf_cnpj' => ['required', 'string', 'max:20', Rule::unique('pessoas', 'cpf_cnpj')->ignore($pessoaId)],
            'rg' => ['nullable', 'string', 'max:20'],
            'rg_orgao_emissor' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'estado_civil' => ['required', Rule::enum(EstadoCivil::class)],
            'regime_bens' => ['nullable', Rule::enum(RegimeBens::class)],
            'email' => ['nullable', 'email', 'max:150'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:20'],
            'cep' => ['nullable', 'string', 'max:10'],
            'logradouro' => ['nullable', 'string', 'max:150'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:50'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', 'string', 'size:2'],
            'tipo_chave_pix' => ['nullable', 'string', 'max:20'],
            'chave_pix' => ['nullable', 'string', 'max:100'],
            'banco' => ['nullable', 'string', 'max:50'],
            'agencia' => ['nullable', 'string', 'max:20'],
            'conta' => ['nullable', 'string', 'max:20'],
            'tipo_conta' => ['required', Rule::enum(TipoConta::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'cpf_cnpj' => 'CPF/CNPJ',
            'rg_orgao_emissor' => 'órgão emissor do RG',
            'estado_civil' => 'estado civil',
            'regime_bens' => 'regime de bens',
            'tipo_chave_pix' => 'tipo de chave PIX',
            'chave_pix' => 'chave PIX',
            'tipo_conta' => 'tipo de conta',
        ];
    }
}
