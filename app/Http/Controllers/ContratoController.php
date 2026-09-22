<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContratoRequest;
use App\Models\Contrato;
use App\Models\Imovel;
use App\Models\Pessoa;
use App\Services\ContratoValorHistoricoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContratoController extends Controller
{
    public function __construct(
        private readonly ContratoValorHistoricoService $historicoValores,
    ) {}

    public function index(Request $request): View
    {
        $busca = $request->input('busca', '');

        $contratos = Contrato::query()
            ->with(['imovel', 'partes.pessoa'])
            ->when($busca, function ($query, $busca) {
                $termo = "%{$busca}%";
                $query->where(function ($q) use ($termo) {
                    $q->where('contratos.id', 'like', $termo)
                        ->orWhereHas('imovel', function ($q2) use ($termo) {
                            $q2->where('codigo', 'like', $termo);
                        })
                        ->orWhereHas('partes', function ($q2) use ($termo) {
                            $q2->where('papel', 'LOCADOR_TITULAR')
                                ->whereHas('pessoa', function ($q3) use ($termo) {
                                    $q3->where('nome', 'like', $termo);
                                });
                        })
                        ->orWhereHas('partes', function ($q2) use ($termo) {
                            $q2->where('papel', 'LOCATARIO_TITULAR')
                                ->whereHas('pessoa', function ($q3) use ($termo) {
                                    $q3->where('nome', 'like', $termo);
                                });
                        });
                });
            })
            ->orderByDesc('contratos.id')
            ->paginate(15)
            ->withQueryString();

        return view('contratos.index', compact('contratos', 'busca'));
    }

    public function create(): View
    {
        [$imoveis, $pessoas] = $this->opcoesFormulario();

        return view('contratos.create', compact('imoveis', 'pessoas'));
    }

    public function store(ContratoRequest $request): RedirectResponse
    {
        $contrato = DB::transaction(function () use ($request): Contrato {
            $contrato = Contrato::create($request->validated());
            $this->historicoValores->registrarValoresIniciais($contrato);

            return $contrato;
        });

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Contrato cadastrado com sucesso. Agora adicione as partes (locador, locatário, fiador).');
    }

    public function show(Contrato $contrato): View
    {
        $contrato->load([
            'imovel.locador',
            'favorecido',
            'partes.pessoa',
            'segurosFiancas',
            'faturas',
            'historicoValores' => fn ($query) => $query
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->with('alteradoPor'),
        ]);

        return view('contratos.show', compact('contrato'));
    }

    public function edit(Contrato $contrato): View
    {
        [$imoveis, $pessoas] = $this->opcoesFormulario();

        return view('contratos.edit', compact('contrato', 'imoveis', 'pessoas'));
    }

    public function update(ContratoRequest $request, Contrato $contrato): RedirectResponse
    {
        $originais = $contrato->only(array_keys(
            ContratoValorHistoricoService::camposMonitorados()
        ));

        DB::transaction(function () use ($request, $contrato, $originais): void {
            $contrato->fill($request->validated());
            $this->historicoValores->registrarAlteracoes($contrato, $originais);
            $contrato->save();
        });

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('sucesso', 'Contrato atualizado com sucesso.');
    }

    public function destroy(Contrato $contrato): RedirectResponse
    {
        $contrato->delete();

        return redirect()
            ->route('contratos.index')
            ->with('sucesso', 'Contrato removido com sucesso.');
    }

    /** @return array{0: Collection, 1: Collection} */
    private function opcoesFormulario(): array
    {
        $imoveis = Imovel::orderBy('codigo')->get(['id', 'codigo', 'logradouro', 'numero', 'locador_id']);
        $pessoas = Pessoa::orderBy('nome')->get(['id', 'nome']);

        return [$imoveis, $pessoas];
    }
}
