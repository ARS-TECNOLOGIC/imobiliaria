<?php

use App\Enums\StatusPagamento;
use App\Models\Contrato;
use App\Models\Fatura;
use App\Models\Imovel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('new invoice displays values suggested by the selected contract', function () {
    $contrato = Contrato::factory()
        ->for(Imovel::factory()->state(['possui_condominio' => true]), 'imovel')
        ->create([
            'valor_aluguel' => 1500.00,
            'valor_condominio' => 250.00,
            'valor_iptu' => 85.50,
            'parcela_iptu' => '03/10',
            'valor_seguro' => 42.75,
        ]);

    $response = $this->get(route('faturas.create', ['contrato_id' => $contrato]));

    $response->assertOk();
    $response->assertSee('value="1500.00"', false);
    $response->assertSee('value="250.00"', false);
    $response->assertSee('value="85.50"', false);
    $response->assertSee('value="03/10"', false);
    $response->assertSee('value="42.75"', false);
});

test('waived late fee is persisted and excluded from the paid amount and administration fee', function () {
    $contrato = Contrato::factory()->create([
        'multa_percentual' => 10.00,
        'taxa_adm_percentual' => 10.00,
    ]);

    $response = $this->post(route('faturas.store'), [
        'contrato_id' => $contrato->id,
        'referencia' => '2026-09-01',
        'data_vencimento' => '2026-09-10',
        'valor_aluguel' => 1000.00,
        'valor_condominio' => 100.00,
        'valor_iptu' => 50.00,
        'valor_seguro' => 25.00,
        'valor_taxa_extra' => 0,
        'valor_desconto' => 0,
        'isento_multa' => true,
        'status_pagamento' => StatusPagamento::PAGO_COM_ATRASO->value,
        'data_recebimento' => '2026-09-15',
    ]);

    $response->assertRedirect();

    $fatura = Fatura::query()->sole();

    expect($fatura->isento_multa)->toBeTrue()
        ->and((float) $fatura->valor_multa_juros)->toBe(0.0)
        ->and((float) $fatura->valor_pago)->toBe(1175.0)
        ->and($fatura->dias_atraso)->toBe(5)
        ->and((float) $fatura->repasse->valor_taxa_adm)->toBe(100.0);
});

test('daily late invoice update keeps waived late fees at zero', function () {
    Carbon::setTestNow('2026-09-18 12:00:00');

    try {
        $contrato = Contrato::factory()->create(['multa_percentual' => 10.00]);
        $fatura = Fatura::factory()->create([
            'contrato_id' => $contrato->id,
            'data_vencimento' => '2026-09-10',
            'isento_multa' => true,
            'status_pagamento' => StatusPagamento::PENDENTE->value,
            'valor_aluguel' => 1000.00,
            'valor_condominio' => 100.00,
            'valor_iptu' => 50.00,
            'valor_seguro' => 25.00,
        ]);

        $this->artisan('faturas:atualizar-vencidas')->assertExitCode(0);

        $fatura->refresh();

        expect($fatura->status_pagamento)->toBe(StatusPagamento::ATRASADO)
            ->and($fatura->dias_atraso)->toBe(8)
            ->and((float) $fatura->valor_multa_juros)->toBe(0.0);
    } finally {
        Carbon::setTestNow();
    }
});
