<?php

namespace App\Http\Controllers;

use App\Enums\StatusPagamento;
use App\Enums\StatusRepasse;
use App\Http\Requests\FaturaRequest;
use App\Models\Contrato;
use App\Models\Fatura;
use App\Services\CalculadoraDiasUteis;
use App\Services\CalculadoraRepasse;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaturaController extends Controller
{
    public function __construct(
        private CalculadoraDiasUteis $diasUteis,
        private CalculadoraRepasse $calculadoraRepasse,
    ) {
    }

    public function index(): View
    {
        $faturas = Fatura::query()
            ->with('contrato.imovel')
            ->orderByDesc('referencia')
            ->paginate(20);

        return view('faturas.index', compact('faturas'));
    }

    public function create(Request $request): View
    {
        $contratos = Contrato::with('imovel')->orderByDesc('id')->get();

        $contratoSelecionado = $request->filled('contrato_id')
            ? Contrato::with('imovel')->find($request->integer('contrato_id'))
            : null;

        return view('faturas.create', compact('contratos', 'contratoSelecionado'));
    }

    public function store(FaturaRequest $request): RedirectResponse
    {
        $dados = $this->processarValoresCalculados($request);

        $fatura = Fatura::create($dados);

        // Repasse nasce com base na estimativa da fatura (ainda não paga);
        // é recalculado automaticamente sempre que a fatura for atualizada
        // (ex.: quando o pagamento é confirmado e o valor real é conhecido).
        $calculo = $this->calculadoraRepasse->calcular($fatura);

        $fatura->repasse()->create([
            'valor_taxa_adm' => $calculo['valor_taxa_adm'],
            'valor_retido' => 0,
            'valor_custos' => 0,
            'valor_total_repasse' => $calculo['valor_total_repasse'],
            'data_limite_repasse' => $fatura->data_vencimento->copy()->addDays(5),
            'status' => StatusRepasse::PENDENTE->value,
        ]);

        return redirect()
            ->route('faturas.show', $fatura)
            ->with('sucesso', 'Fatura cadastrada com sucesso, com o repasse gerado automaticamente.');
    }

    public function show(Fatura $fatura): View
    {
        $fatura->load('contrato.imovel', 'contrato.favorecido', 'repasse', 'documentos');

        return view('faturas.show', compact('fatura'));
    }

    public function edit(Fatura $fatura): View
    {
        $contratos = Contrato::with('imovel')->orderByDesc('id')->get();

        return view('faturas.edit', compact('fatura', 'contratos'));
    }

    public function update(FaturaRequest $request, Fatura $fatura): RedirectResponse
    {
        $dados = $this->processarValoresCalculados($request);

        $fatura->update($dados);

        // O valor pago (ou a multa) pode ter mudado ao salvar — recalcula o
        // repasse mantendo a retenção/custos que já estavam lançados nele.
        if ($fatura->repasse) {
            $calculo = $this->calculadoraRepasse->calcular(
                $fatura,
                (float) $fatura->repasse->valor_retido,
                (float) $fatura->repasse->valor_custos,
            );

            $fatura->repasse->update([
                'valor_taxa_adm' => $calculo['valor_taxa_adm'],
                'valor_total_repasse' => $calculo['valor_total_repasse'],
            ]);
        }

        return redirect()
            ->route('faturas.show', $fatura)
            ->with('sucesso', 'Fatura atualizada com sucesso.');
    }

    public function destroy(Fatura $fatura): RedirectResponse
    {
        // O repasse é removido automaticamente (onDelete cascade na migration).
        $fatura->delete();

        return redirect()
            ->route('faturas.index')
            ->with('sucesso', 'Fatura removida com sucesso.');
    }

    /**
     * Centraliza os campos que o usuário NÃO digita diretamente: dias_atraso,
     * valor_multa_juros e valor_pago. Todos são derivados dos outros valores
     * da fatura — uma única fonte de verdade em vez de espalhar a conta pela
     * store() e pela update().
     */
    private function processarValoresCalculados(FaturaRequest $request): array
    {
        $dados = $request->validated();
        $contrato = Contrato::findOrFail($dados['contrato_id']);
        $isentoMulta = $request->boolean('isento_multa');

        $diasAtraso = $this->calcularDiasAtraso($dados);
        $multa = $isentoMulta ? 0.0 : $this->calcularMulta($contrato, $dados, $diasAtraso);

        $dados['dias_atraso'] = $diasAtraso;
        $dados['isento_multa'] = $isentoMulta;
        $dados['valor_multa_juros'] = $multa;
        $dados['valor_pago'] = $this->calcularValorPago($dados, $multa);

        return $dados;
    }

    private function calcularDiasAtraso(array $dados): int
    {
        $vencimento = $this->diasUteis->proximoDiaUtil(
            Carbon::parse($dados['data_vencimento'])->startOfDay()
        );

        $referencia = match ($dados['status_pagamento']) {
            StatusPagamento::PAGO->value,
            StatusPagamento::PAGO_COM_ATRASO->value => isset($dados['data_recebimento'])
                ? Carbon::parse($dados['data_recebimento'])->startOfDay()
                : $vencimento,
            StatusPagamento::ATRASADO->value => today(),
            default => $vencimento,
        };

        return max(0, $vencimento->diffInDays($referencia, false));
    }

    private function calcularMulta(Contrato $contrato, array $dados, int $diasAtraso): float
    {
        if ($diasAtraso <= 0) {
            return 0.0;
        }

        return round($this->baseDeCalculo($dados) * ((float) $contrato->multa_percentual / 100), 2);
    }

    private function calcularValorPago(array $dados, float $multa): ?float
    {
        if (! in_array($dados['status_pagamento'], [StatusPagamento::PAGO->value, StatusPagamento::PAGO_COM_ATRASO->value], true)) {
            return null;
        }

        $total = $this->baseDeCalculo($dados)
            + (float) ($dados['valor_taxa_extra'] ?? 0)
            + $multa
            - (float) ($dados['valor_desconto'] ?? 0);

        return round($total, 2);
    }

    private function baseDeCalculo(array $dados): float
    {
        return (float) $dados['valor_aluguel']
            + (float) ($dados['valor_condominio'] ?? 0)
            + (float) ($dados['valor_iptu'] ?? 0)
            + (float) ($dados['valor_seguro'] ?? 0);
    }
}
