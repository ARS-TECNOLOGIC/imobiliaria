<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\ImovelServicoCondominio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImovelServicoCondominioController extends Controller
{
    /** Adiciona um serviço de condomínio a um imóvel (chamado direto da tela de show do Imóvel). */
    public function store(Request $request, Imovel $imovel): RedirectResponse
    {
        $dados = $request->validate([
            'servico' => ['required', 'string', 'max:20'],
            'descricao_outro' => ['nullable', 'string', 'max:100'],
            'tipo_cobranca' => ['required', 'string', 'max:30'],
            'observacao' => ['nullable', 'string', 'max:255'],
        ]);

        $imovel->servicosCondominio()->create($dados);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('sucesso', 'Serviço adicionado ao condomínio.');
    }

    public function destroy(ImovelServicoCondominio $servico): RedirectResponse
    {
        $imovel = $servico->imovel;
        $servico->delete();

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('sucesso', 'Serviço removido.');
    }
}
