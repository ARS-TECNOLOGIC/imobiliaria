<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record a rule with `record-rule` only when the user explicitly asks for one. Instructions for the work at hand are not rules, no matter how emphatic: "remove this typo", "use X here" are work to do, not rules to record. Never record a rule on your own initiative, as a byproduct of a change, or to summarize what you just did. When the user does ask, pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Use `record-rule` rather than your native memory or notes tool, because native memory is personal and session-scoped, while only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

</laravel-boost-guidelines>

<!-- ==================================================================== -->

# Contexto específico do projeto (Sistema de Locação de Imóveis)

O bloco acima foi gerado pelo Laravel Boost (convenções gerais do
Laravel/PHP/Pest/Pint). Tudo abaixo é conhecimento de domínio específico
deste projeto — leia antes de tocar em models, controllers ou migrations
relacionados a locação. Várias decisões aqui não são óbvias olhando só
pros arquivos.

## Domínio

Gestão de locação de imóveis (imobiliária) em Bauru/SP: cadastro de pessoas
(que podem ser locador, locatário, fiador ou corretor), imóveis, contratos
de locação, faturas mensais e repasse do valor ao locador.

## Estrutura de dados (13 tabelas + adições)

`pessoas`, `pessoa_relacionamentos`, `imoveis`, `imovel_servicos_condominio`,
`contratos`, `contrato_partes`, `contrato_historico_valores`,
`contrato_seguros_fiancas`, `faturas`, `repasses`, `documentos` (polimórfica),
`corretores`, `corretor_imoveis`, `feriados`.

Colunas adicionadas depois da migration inicial:
- `contratos.multa_percentual` (decimal, default 10.00)
- `faturas.isento_multa` (boolean, default false)

Models espelham 1:1 essas tabelas em `app/Models`. Enums PHP (backed,
string) em `app/Enums` para toda coluna `enum` do banco (16 enums).

## Regras de negócio importantes (não estão óbvias no código sem contexto)

1. **Pluralização em português quebra convenções do Eloquent/Laravel.**
   O inflector do Laravel é otimizado pra inglês. Já corrigido para:
   - Model `Imovel`: `protected $table = 'imoveis';`
   - Model `Corretor`: `protected $table = 'corretores';`
   - Rota de imóveis precisa de `->parameters(['imoveis' => 'imovel'])`
     no `Route::resource`, senão o model binding quebra (erro 500 em
     `show`/`edit`). **Se criar o controller de Corretores, espere o
     mesmo problema e aplique a mesma correção na rota.**

2. **Soft delete + histórico.** `Pessoa` usa `SoftDeletes`. Relacionamentos
   que precisam manter o histórico mesmo se a pessoa for removida usam
   `->withTrashed()`: `Imovel::locador()`, `Contrato::favorecido()`,
   `ContratoParte::pessoa()`. Mesmo assim, views usam `?->` e fallback
   `'Pessoa removida'` por segurança.

3. **Checkbox desmarcado não é enviado no POST.** Sempre usar
   `$request->boolean('campo')` no controller para booleans
   (`possui_condominio`, `isento_multa`), nunca confiar em
   `$request->validated()['campo']` sozinho.

4. **Campo `disabled` não é enviado no formulário.** Padrão usado no
   `contrato_id` da fatura (imutável após criada): select visualmente
   `disabled` + um `<input type="hidden">` paralelo com o mesmo `name`
   carregando o valor real.

5. **Campos calculados nunca são inputs editáveis pelo usuário.** Em
   `Fatura`: `dias_atraso`, `valor_multa_juros` e `valor_pago` são sempre
   calculados no `FaturaController::processarValoresCalculados()` —
   nunca confiar em valor vindo do form pra esses três campos. O
   formulário mostra prévia em JS (mesma lógica espelhada no `<script>`
   de `resources/views/faturas/_form.blade.php`), mas quem decide o valor
   gravado é sempre o backend.

6. **Multa por atraso**: percentual configurável por contrato
   (`Contrato::multa_percentual`, default 10%), aplicada **uma vez** (não
   por dia) sobre `aluguel + condominio + iptu + seguro`, só se houver
   atraso E a fatura não estiver com `isento_multa = true`. Atraso é
   calculado sobre o **dia útil efetivo** (ver item 8).

7. **Regra do repasse** — centralizada em `App\Services\CalculadoraRepasse`:
   - `valor_taxa_adm` = `(aluguel + multa) * contrato.taxa_adm_percentual / 100`
     (a taxa de administração incide também sobre a multa, quando existir)
   - `valor_total_repasse` = `valor_total_recebido - valor_retido - valor_taxa_adm`
     (`valor_total_recebido` = `fatura.valor_pago` se já paga, senão uma
     estimativa do total previsto da fatura)
   - `valor_custos` **não entra** nessa conta — serve só pra exibir
     "líquido da administradora" (`taxa_adm - custos`), informativo, não
     persistido, calculado direto na view.
   - Repasse é criado automaticamente em `FaturaController::store()` e
     **recalculado automaticamente** (mantendo retido/custos) em
     `FaturaController::update()`, porque `valor_pago`/multa podem mudar
     depois que a fatura já existe.

8. **Dia útil e feriados** — `App\Services\CalculadoraDiasUteis` consulta
   a tabela `feriados` (fixos + móveis calculados a partir da Páscoa via
   `easter_date()` nativa do PHP, seedados por `FeriadosSeeder`) e empurra
   uma data para o próximo dia útil (`proximoDiaUtil()`). Toda comparação
   de vencimento (multa, dias de atraso, comando diário) usa essa data
   ajustada, não a data crua do contrato. Carnaval/Sexta-feira
   Santa/Corpus Christi estão na tabela como feriado (não são feriados
   federais por lei, mas bancos tratam como não-úteis para boleto —
   decisão deliberada, documentada no `FeriadosSeeder`).

9. **Condomínio/IPTU não pré-preenchem cegamente na fatura.** Ao criar
   fatura a partir de um contrato: `valor_condominio` só sugere o valor
   do contrato se `Imovel::possui_condominio` for `true` (senão sugere 0);
   `valor_iptu` **nunca** pré-preenche (fica em 0), porque IPTU não é
   cobrado todo mês — força confirmação manual.

10. **Comando `faturas:atualizar-vencidas`** (em
    `app/Console/Commands/AtualizarFaturasVencidas.php`) marca faturas
    `PENDENTE` vencidas (respeitando dia útil) como `ATRASADO` e recalcula
    multa/dias de atraso das que já estavam atrasadas. Precisa estar
    agendado em `routes/console.php` via
    `Schedule::command(AtualizarFaturasVencidas::class)->dailyAt('01:00')`
    **e** precisa de um cron real do SO chamando
    `php artisan schedule:run` a cada minuto em produção (Laragon não faz
    isso por padrão — só relevante fora do ambiente de dev).

## Padrão de tela (Blade)

Cada entidade principal (Pessoa, Imovel, Contrato, Fatura) tem: `index`,
`create`, `edit`, `show` + `_form.blade.php` parcial reutilizado por
`create`/`edit`. Entidades subordinadas (partes do contrato,
seguro/fiança, serviços de condomínio, repasse) são geridas **dentro** da
tela `show` da entidade pai, não em telas próprias — sempre com um form de
adicionar + lista com botão de remover.

Layout único em `resources/views/layouts/app.blade.php`: navbar com os
links das entidades, banner de sucesso (`session('sucesso')`) e um banner
genérico de erros de validação (`$errors->any()`) — importante pra
detectar campo obrigatório faltando em formulário sem `@error` individual.

## O que falta (próximos passos conhecidos)

- **Corretores**: model, migration e factory existem, mas **não há
  controller nem views ainda**. É a última entidade principal sem CRUD.
  Atenção ao mesmo problema de pluralização de rota do item 1.
- **Documentos (anexos)**: model e migration existem (relação
  polimórfica), mas não há controller/UI de upload ainda.
- **Locale PT-BR pendente de decisão**: se `APP_LOCALE=pt_BR` estiver no
  `.env` sem o pacote de tradução instalado, mensagens de validação
  aparecem como chave crua (`validation.min.numeric`). Duas opções
  levantadas e ainda não escolhidas pelo usuário:
  (a) voltar pra `APP_LOCALE=en`, ou
  (b) `composer require laravel-lang/common --dev` +
  `php artisan lang:add pt_BR`.
- **IPTU por mês**: hoje `parcela_iptu` é texto livre (ex: "03/10"). Não
  há lógica que saiba em quais meses do ano o IPTU é cobrado — só um
  lembrete visual no formulário. Melhoria possível, não implementada.
- Sem "juros de mora" diário — só a multa percentual única implementada.

## Rotas que precisam estar em `routes/web.php`

```php
Route::resource('pessoas', PessoaController::class);

Route::resource('imoveis', ImovelController::class)
    ->parameters(['imoveis' => 'imovel']);
Route::post('imoveis/{imovel}/servicos', [ImovelServicoCondominioController::class, 'store'])
    ->name('imoveis.servicos.store');
Route::delete('imovel-servicos/{servico}', [ImovelServicoCondominioController::class, 'destroy'])
    ->name('imovel-servicos.destroy');

Route::resource('contratos', ContratoController::class);
Route::post('contratos/{contrato}/partes', [ContratoParteController::class, 'store'])
    ->name('contratos.partes.store');
Route::delete('contrato-partes/{parte}', [ContratoParteController::class, 'destroy'])
    ->name('contrato-partes.destroy');
Route::post('contratos/{contrato}/seguros', [ContratoSeguroFiancaController::class, 'store'])
    ->name('contratos.seguros.store');
Route::delete('contrato-seguros/{seguro}', [ContratoSeguroFiancaController::class, 'destroy'])
    ->name('contrato-seguros.destroy');

Route::resource('faturas', FaturaController::class);
Route::put('repasses/{repasse}', [RepasseController::class, 'update'])
    ->name('repasses.update');
```

## Seeders

`FeriadosSeeder` deve rodar **antes** de `SistemaLocacaoSeeder` (ambos
chamados em `DatabaseSeeder::run()`). `SistemaLocacaoSeeder` monta um
cenário realista: corretores, locadores, locatários, fiadores, imóveis,
contratos com partes/seguro conforme a garantia, faturas dos últimos
meses com status variado, e os repasses correspondentes.

`config/app.php` usa `env('APP_FAKER_LOCALE', 'en_US')` — defina
`APP_FAKER_LOCALE=pt_BR` no `.env` para as factories gerarem CPF/CNPJ e
endereços em formato brasileiro.

## Nota sobre `.gitignore`

A documentação do Laravel Boost sugere adicionar `AGENTS.md`,
`.mcp.json` e `boost.json` ao `.gitignore`, já que são regenerados a cada
`boost:install`/`boost:update`. **Não siga essa sugestão neste projeto**:
a seção de contexto de domínio abaixo do bloco `<laravel-boost-guidelines>`
não é regenerada automaticamente por esses comandos — é conhecimento do
projeto que vale versionar. Se rodar `boost:update` no futuro, ele deve
sobrescrever só o bloco `<laravel-boost-guidelines>...</laravel-boost-guidelines>`;
confira o diff e reanexe esta seção caso ela seja removida.
