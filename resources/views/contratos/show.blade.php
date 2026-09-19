@extends('layouts.app')

@section('titulo', 'Contrato #' . $contrato->id)

@section('conteudo')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">
            Contrato #{{ $contrato->id }}
            <span class="badge bg-{{ $contrato->status->value === 'ATIVO' ? 'success' : 'secondary' }}">
                {{ $contrato->status->value }}
            </span>
        </h1>
        <div>
            <a href="{{ route('contratos.edit', $contrato) }}" class="btn btn-outline-primary">Editar</a>
            <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <h2 class="h5">Dados do contrato</h2>
            <dl class="row">
                <dt class="col-sm-5">Imóvel</dt>
                <dd class="col-sm-7">{{ $contrato->imovel->codigo }} — {{ $contrato->imovel->logradouro }}, {{ $contrato->imovel->numero }}</dd>

                <dt class="col-sm-5">Favorecido do repasse</dt>
                <dd class="col-sm-7">{{ $contrato->favorecido?->nome ?? 'Pessoa removida' }}</dd>

                <dt class="col-sm-5">Vigência</dt>
                <dd class="col-sm-7">
                    {{ $contrato->data_inicio->format('d/m/Y') }} até
                    {{ $contrato->data_fim?->format('d/m/Y') ?? 'em aberto' }}
                </dd>

                <dt class="col-sm-5">Dia de vencimento</dt>
                <dd class="col-sm-7">Todo dia {{ $contrato->dia_vencimento }}</dd>

                <dt class="col-sm-5">Garantia</dt>
                <dd class="col-sm-7">{{ ucfirst(strtolower(str_replace('_', ' ', $contrato->garantia->value))) }}</dd>

                <dt class="col-sm-5">Aluguel</dt>
                <dd class="col-sm-7">R$ {{ number_format($contrato->valor_aluguel, 2, ',', '.') }}</dd>

                <dt class="col-sm-5">Condomínio / IPTU / Seguro</dt>
                <dd class="col-sm-7">
                    R$ {{ number_format($contrato->valor_condominio, 2, ',', '.') }} /
                    R$ {{ number_format($contrato->valor_iptu, 2, ',', '.') }} /
                    R$ {{ number_format($contrato->valor_seguro, 2, ',', '.') }}
                </dd>

                <dt class="col-sm-5">Taxa de administração</dt>
                <dd class="col-sm-7">{{ $contrato->taxa_adm_percentual }}%</dd>
            </dl>
        </div>

        <div class="col-md-6">
            <h2 class="h5">Partes do contrato</h2>
            <ul class="list-group mb-3">
                @forelse ($contrato->partes as $parte)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $parte->pessoa?->nome ?? 'Pessoa removida' }}
                            <span class="badge bg-secondary">{{ str_replace('_', ' ', $parte->papel->value) }}</span>
                            @unless ($parte->assina_contrato)
                                <span class="badge bg-warning text-dark">não assina</span>
                            @endunless
                        </span>
                        <form action="{{ route('contrato-partes.destroy', $parte) }}" method="POST"
                              onsubmit="return confirm('Remover esta parte do contrato?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhuma parte cadastrada ainda.</li>
                @endforelse
            </ul>

            <form action="{{ route('contratos.partes.store', $contrato) }}" method="POST" class="row g-2 mb-4">
                @csrf
                <div class="col-md-5">
                    <select name="pessoa_id" class="form-select @error('pessoa_id') is-invalid @enderror" required>
                        <option value="">Selecione a pessoa...</option>
                        @foreach (\App\Models\Pessoa::orderBy('nome')->get(['id', 'nome']) as $pessoa)
                            <option value="{{ $pessoa->id }}">{{ $pessoa->nome }}</option>
                        @endforeach
                    </select>
                    @error('pessoa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <select name="papel" class="form-select" required>
                        @foreach (\App\Enums\PapelContrato::cases() as $opcao)
                            <option value="{{ $opcao->value }}">{{ str_replace('_', ' ', $opcao->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="assina_contrato" value="1" checked class="form-check-input" id="assina">
                        <label class="form-check-label" for="assina">Assina</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">+</button>
                </div>
            </form>

            <h2 class="h5 d-flex justify-content-between align-items-center">
                Faturas
                <a href="{{ route('faturas.create', ['contrato_id' => $contrato->id]) }}" class="btn btn-sm btn-outline-primary">Nova fatura</a>
            </h2>
            <ul class="list-group mb-4">
                @forelse ($contrato->faturas as $fatura)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('faturas.show', $fatura) }}">{{ $fatura->referencia->format('m/Y') }}</a>
                        <span class="badge bg-secondary">{{ str_replace('_', ' ', $fatura->status_pagamento->value) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Nenhuma fatura gerada.</li>
                @endforelse
            </ul>

            @if (in_array($contrato->garantia, [\App\Enums\Garantia::SEGURO_FIANCA, \App\Enums\Garantia::TITULO_CAPITALIZACAO]))
                <h2 class="h5">Seguro / Fiança</h2>
                <ul class="list-group mb-3">
                    @forelse ($contrato->segurosFiancas as $seguro)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                {{ $seguro->seguradora }} — apólice {{ $seguro->numero_apolice ?? 's/n' }}
                                <span class="badge bg-secondary">{{ str_replace('_', ' ', $seguro->status->value) }}</span>
                                <div class="small text-muted">vence em {{ $seguro->data_vencimento_apolice->format('d/m/Y') }}</div>
                            </span>
                            <form action="{{ route('contrato-seguros.destroy', $seguro) }}" method="POST"
                                  onsubmit="return confirm('Remover esta apólice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Nenhuma apólice cadastrada.</li>
                    @endforelse
                </ul>

                <form action="{{ route('contratos.seguros.store', $contrato) }}" method="POST" class="row g-2">
                    @csrf
                    <input type="hidden" name="status" value="ATIVO">
                    <div class="col-md-3">
                        <select name="tipo" class="form-select" required>
                            @foreach (\App\Enums\TipoSeguro::cases() as $opcao)
                                <option value="{{ $opcao->value }}">{{ str_replace('_', ' ', $opcao->value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="seguradora" placeholder="Seguradora" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="numero_apolice" placeholder="Nº apólice" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="data_inicio" class="form-control" required title="Início da apólice">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="data_vencimento_apolice" class="form-control" required title="Vencimento da apólice">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-outline-primary">Adicionar apólice</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- SEÇÃO: Documentos — Explorer de arquivos                               --}}
    {{-- ====================================================================== --}}
    @php
        $documentos = $contrato->documentos->sortBy('categoria_armazenamento');
        $pastas = $documentos->groupBy('categoria_armazenamento');
    @endphp

    <div class="ds-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ds-section-title mb-0">
                <span class="ds-section-icon">&#128194;</span>
                Documentos
            </h2>
            <span class="badge bg-light text-dark border">{{ $documentos->count() }} arquivo(s)</span>
        </div>

        {{-- Formulário de upload --}}
        <div class="ds-card mb-4">
            <div class="ds-card-header d-flex align-items-center gap-2">
                <span class="ds-card-icon">&#8682;</span>
                <strong>Enviar novo documento</strong>
            </div>
            <div class="ds-card-body">
                <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" id="doc-upload-form">
                    @csrf
                    <input type="hidden" name="documentavel_type" value="{{ App\Models\Contrato::class }}">
                    <input type="hidden" name="documentavel_id" value="{{ $contrato->id }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-2 col-md-4">
                            <label class="ds-label">Pasta de destino</label>
                            <select name="categoria_armazenamento" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach (\App\Enums\CategoriaArmazenamento::cases() as $cat)
                                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4">
                            <label class="ds-label">Tipo</label>
                            <select name="tipo_documento" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach (\App\Enums\TipoDocumento::cases() as $tipo)
                                    <option value="{{ $tipo->value }}">{{ str_replace('_', ' ', $tipo->value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <label class="ds-label">Arquivo</label>
                            <input type="file" name="arquivo" accept=".pdf,.jpeg,.jpg,.png"
                                   class="form-control" required id="doc-file-input">
                            <div class="form-text">PDF, JPEG ou PNG — max. 10 MB</div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="ds-label">Descricao <span class="text-muted">(opcional)</span></label>
                            <input type="text" name="descricao" placeholder="Ex: Contrato assinado em 01/09"
                                   class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <button type="submit" class="btn btn-primary w-100" id="doc-upload-btn">
                                <span class="btn-text">Enviar</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm"></span> Enviando...
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="ds-upload-progress d-none mt-3" id="doc-upload-progress">
                        <div class="ds-progress-bar">
                            <div class="ds-progress-fill" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Explorador de arquivos --}}
        <div class="ds-card">
            {{-- Barra de ferramentas --}}
            <div class="ds-toolbar" id="doc-toolbar">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check ds-checkbox mb-0">
                        <input type="checkbox" id="doc-select-all" class="form-check-input">
                        <label class="form-check-label" for="doc-select-all">Selecionar todos</label>
                    </div>
                    <span class="ds-text-muted small d-none" id="doc-selected-count"></span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm" id="btn-download-selected" disabled>
                        <span>&#8595;</span> Baixar
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-remove-selected" disabled>
                        <span>&#10005;</span> Remover
                    </button>
                </div>
            </div>

            @if ($documentos->isEmpty())
                {{-- Estado vazio --}}
                <div class="ds-empty-state">
                    <div class="ds-empty-icon">&#128194;</div>
                    <p class="ds-empty-title">Nenhum documento anexado</p>
                    <p class="ds-empty-text">Envie o primeiro documento usando o formulario acima.</p>
                </div>
            @else
                <div class="ds-explorer">
                    {{-- Sidebar: arvore de pastas --}}
                    <div class="ds-explorer-sidebar" id="folder-tree">
                        <div class="ds-sidebar-header">Pastas</div>
                        <button type="button"
                                class="ds-folder-item active"
                                data-folder="all">
                            <span class="ds-folder-icon">&#128193;</span>
                            <span class="ds-folder-name">Todos os documentos</span>
                            <span class="ds-folder-count">{{ $documentos->count() }}</span>
                        </button>
                        @foreach ($pastas as $categoria => $docs)
                            @if ($categoria)
                                <button type="button"
                                        class="ds-folder-item"
                                        data-folder="{{ $categoria }}">
                                    <span class="ds-folder-icon">&#128194;</span>
                                    <span class="ds-folder-name">{{ \App\Enums\CategoriaArmazenamento::from($categoria)->label() }}</span>
                                    <span class="ds-folder-count">{{ $docs->count() }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>

                    {{-- Lista de arquivos --}}
                    <div class="ds-explorer-content">
                        <table class="ds-file-table" id="file-table">
                            <thead>
                                <tr>
                                    <th class="col-check"></th>
                                    <th class="col-icon"></th>
                                    <th class="col-name">Nome</th>
                                    <th class="col-type">Tipo</th>
                                    <th class="col-size">Tamanho</th>
                                    <th class="col-actions">Acoes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documentos as $doc)
                                    @php
                                        $ext = strtoupper(pathinfo($doc->nome_original, PATHINFO_EXTENSION));
                                        $isPdf = $doc->mime_type === 'application/pdf';
                                        $isImage = str_starts_with($doc->mime_type ?? '', 'image/');
                                    @endphp
                                    <tr class="file-row" data-folder="{{ $doc->categoria_armazenamento }}">
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   class="form-check-input doc-checkbox"
                                                   value="{{ $doc->id }}"
                                                   data-url="{{ route('documentos.download', $doc) }}">
                                        </td>
                                        <td class="text-center">
                                            <div class="ds-file-icon {{ $isPdf ? 'ds-file-icon--pdf' : ($isImage ? 'ds-file-icon--image' : 'ds-file-icon--default') }}">
                                                {{ $isPdf ? 'PDF' : ($isImage ? 'IMG' : $ext) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="ds-file-name">{{ $doc->nome_original }}</div>
                                            @if ($doc->descricao)
                                                <div class="ds-file-desc">{{ $doc->descricao }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $ext }}</span>
                                        </td>
                                        <td class="ds-text-muted">
                                            {{ $doc->tamanho_bytes >= 1048576
                                                ? number_format($doc->tamanho_bytes / 1048576, 1, ',', '.') . ' MB'
                                                : number_format($doc->tamanho_bytes / 1024, 0) . ' KB' }}
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('documentos.download', $doc) }}"
                                                   class="btn btn-sm btn-outline-success"
                                                   title="Baixar">&#8595;</a>
                                                <form action="{{ route('documentos.destroy', $doc) }}" method="POST"
                                                      class="d-inline" onsubmit="return confirm('Remover este documento?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Remover">&#10005;</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
    (function () {
        const selectAll = document.getElementById('doc-select-all');
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        const countEl = document.getElementById('doc-selected-count');
        const btnDownload = document.getElementById('btn-download-selected');
        const btnRemove = document.getElementById('btn-remove-selected');
        const folderItems = document.querySelectorAll('.ds-folder-item');
        const fileRows = document.querySelectorAll('.file-row');
        const uploadForm = document.getElementById('doc-upload-form');
        const uploadBtn = document.getElementById('doc-upload-btn');

        function getSelectedIds() {
            return Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        }

        function getVisibleRows() {
            return Array.from(fileRows).filter(r => r.style.display !== 'none');
        }

        function updateUI() {
            const ids = getSelectedIds();
            const visibleIds = getVisibleRows().map(r => r.querySelector('.doc-checkbox')?.value).filter(Boolean);
            const allVisibleSelected = visibleIds.length > 0 && visibleIds.every(id => ids.includes(id));

            countEl.textContent = ids.length > 0 ? `${ids.length} selecionado(s)` : '';
            countEl.classList.toggle('d-none', ids.length === 0);
            btnDownload.disabled = ids.length === 0;
            btnRemove.disabled = ids.length === 0;

            if (selectAll) {
                selectAll.checked = allVisibleSelected;
                selectAll.indeterminate = ids.length > 0 && !allVisibleSelected;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                getVisibleRows().forEach(row => {
                    const cb = row.querySelector('.doc-checkbox');
                    if (cb) cb.checked = selectAll.checked;
                });
                updateUI();
            });
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateUI));

        // Navegacao de pastas
        folderItems.forEach(item => {
            item.addEventListener('click', function () {
                folderItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                const folder = this.dataset.folder;
                fileRows.forEach(row => {
                    row.style.display = (folder === 'all' || row.dataset.folder === folder) ? '' : 'none';
                });

                checkboxes.forEach(cb => cb.checked = false);
                updateUI();
            });
        });

        // Download em massa
        if (btnDownload) {
            btnDownload.addEventListener('click', function () {
                getSelectedIds().forEach(id => {
                    const cb = document.querySelector(`.doc-checkbox[value="${id}"]`);
                    if (cb?.dataset.url) window.open(cb.dataset.url, '_blank');
                });
            });
        }

        // Remocao em massa
        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                const ids = getSelectedIds();
                if (ids.length === 0) return;
                if (!confirm(`Remover ${ids.length} documento(s)? Esta acao nao pode ser desfeita.`)) return;

                Promise.all(ids.map(id =>
                    fetch(`/documentos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                )).then(() => window.location.reload());
            });
        }

        // Upload: feedback visual
        if (uploadForm) {
            uploadForm.addEventListener('submit', function () {
                uploadBtn.querySelector('.btn-text').classList.add('d-none');
                uploadBtn.querySelector('.btn-loading').classList.remove('d-none');
                uploadBtn.disabled = true;
            });
        }
    })();
    </script>
@endsection
