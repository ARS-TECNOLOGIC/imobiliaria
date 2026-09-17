<?php

namespace Database\Seeders;

use App\Models\Feriado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FeriadosSeeder extends Seeder
{
    public function run(): void
    {
        $anoAtual = (int) date('Y');

        // Gera feriados do ano anterior até 2 anos no futuro, o suficiente
        // pra cobrir contratos vigentes e vencimentos próximos.
        foreach (range($anoAtual - 1, $anoAtual + 2) as $ano) {
            foreach ($this->feriadosDoAno($ano) as $feriado) {
                Feriado::query()->updateOrCreate(
                    ['data' => $feriado['data']],
                    ['descricao' => $feriado['descricao'], 'nacional' => $feriado['nacional']]
                );
            }
        }
    }

    /** @return array<int, array{data: string, descricao: string, nacional: bool}> */
    private function feriadosDoAno(int $ano): array
    {
        // easter_date() é uma função nativa do PHP (extensão calendar) que
        // retorna o timestamp da Páscoa (domingo) para o ano informado.
        $pascoa = Carbon::createFromTimestamp(easter_date($ano));

        $fixos = [
            ["{$ano}-01-01", 'Ano Novo'],
            ["{$ano}-04-21", 'Tiradentes'],
            ["{$ano}-05-01", 'Dia do Trabalho'],
            ["{$ano}-09-07", 'Independência do Brasil'],
            ["{$ano}-10-12", 'Nossa Senhora Aparecida'],
            ["{$ano}-11-02", 'Finados'],
            ["{$ano}-11-15", 'Proclamação da República'],
            ["{$ano}-11-20", 'Dia Nacional de Zumbi e da Consciência Negra'],
            ["{$ano}-12-25", 'Natal'],
        ];

        $moveis = [
            [$pascoa->copy()->subDays(47)->toDateString(), 'Carnaval (segunda-feira)'],
            [$pascoa->copy()->subDays(46)->toDateString(), 'Carnaval (terça-feira)'],
            [$pascoa->copy()->subDays(2)->toDateString(), 'Sexta-feira Santa'],
            [$pascoa->copy()->addDays(60)->toDateString(), 'Corpus Christi'],
        ];

        return collect([...$fixos, ...$moveis])
            ->map(fn ($f) => ['data' => $f[0], 'descricao' => $f[1], 'nacional' => true])
            ->all();
    }
}
