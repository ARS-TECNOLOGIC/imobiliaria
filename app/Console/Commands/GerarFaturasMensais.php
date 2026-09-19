<?php

namespace App\Console\Commands;

use App\Services\CalculadoraDiasUteis;
use App\Services\GeradoraFaturasMensais;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('faturas:gerar-mensais {--mes= : Mês de referência (Y-m). Padrão: mês atual}')]
#[Description('Gera faturas para contratos ativos no mês de referência informado.')]
class GerarFaturasMensais extends Command
{
    public function __construct(
        private GeradoraFaturasMensais $geradora,
        private CalculadoraDiasUteis $diasUteis,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $mes = $this->option('mes');

        $referencia = $mes
            ? Carbon::parse($mes)->startOfMonth()
            : now()->startOfMonth();

        $this->info("Gerando faturas para referência {$referencia->format('Y-m')}...");

        $total = $this->geradora->gerar($referencia);

        $this->info("{$total} fatura(s) gerada(s) com sucesso.");

        return self::SUCCESS;
    }
}
