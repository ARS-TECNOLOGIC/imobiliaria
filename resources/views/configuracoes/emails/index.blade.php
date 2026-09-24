{{-- Modelos de E-mail --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Modelos de E-mail</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoModelo">
        <i class="fa-solid fa-plus me-1"></i> Novo Modelo
    </button>
</div>

@foreach ($escopos as $escopoChave => $escopoNome)
    @php
        $modelosEscopo = $modelos->get($escopoChave, collect());
    @endphp

    <div class="card mb-4">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="fa-solid fa-layer-group me-1 text-primary"></i>
                {{ $escopoNome }}
            </h6>
            <span class="badge bg-secondary">{{ $modelosEscopo->count() }} modelo(s)</span>
        </div>
        <div class="card-body p-0">
            @if ($modelosEscopo->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-inbox fa-2x mb-2"></i>
                    <p class="mt-2">Nenhum modelo cadastrado para este escopo.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary btn-novo-modelo" data-bs-toggle="modal" data-bs-target="#modalNovoModelo" data-escopo="{{ $escopoChave }}">
                        <i class="fa-solid fa-plus me-1"></i> Criar primeiro modelo
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Nome</th>
                                <th>Assunto</th>
                                <th>Remetente</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 120px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modelosEscopo as $modelo)
                                <tr>
                                    <td>
                                        @if ($modelo->sistema)
                                            <i class="fa-solid fa-shield-halved text-primary" title="Modelo de sistema"></i>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $modelo->nome }}</td>
                                    <td class="text-truncate" style="max-width: 300px;">{{ $modelo->assunto }}</td>
                                    <td class="text-muted small">{{ $modelo->remetente_nome }} <{{ $modelo->remetente_email }}></td>
                                    <td>
                                        @if ($modelo->sistema)
                                            <span class="badge bg-primary">Sistema</span>
                                        @elseif ($modelo->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="acoes-cell">
                                            <a href="{{ route('configuracoes.emails.show', $modelo) }}" class="btn-icon view" title="Visualizar">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a href="{{ route('configuracoes.emails.edit', $modelo) }}" class="btn-icon edit" title="Editar">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                            @if (! $modelo->sistema)
                                                <button type="button" class="btn-icon delete btn-excluir-modelo"
                                                        title="Excluir"
                                                        data-id="{{ $modelo->id }}"
                                                        data-nome="{{ $modelo->nome }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endforeach

{{-- Assinaturas de E-mail --}}
<div class="d-flex justify-content-between align-items-center mb-3 mt-5">
    <h2 class="h4 mb-0">Assinaturas de E-mail</h2>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNovaAssinatura">
        <i class="fa-solid fa-plus me-1"></i> Nova Assinatura
    </button>
</div>

<div class="card mb-4">
    <div class="card-body p-0">
        @if ($assinaturas->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-signature fa-2x mb-2"></i>
                <p class="mt-2 mb-0">Nenhuma assinatura cadastrada.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Nome</th>
                            <th>Conteúdo</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assinaturas as $assinatura)
                            <tr>
                                <td>
                                    @if ($assinatura->sistema)
                                        <i class="fa-solid fa-shield-halved text-primary" title="Assinatura de sistema"></i>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $assinatura->nome }}</td>
                                <td class="text-truncate text-muted small" style="max-width: 300px;">{{ strip_tags($assinatura->conteudo) }}</td>
                                <td>
                                    @if ($assinatura->sistema)
                                        <span class="badge bg-primary">Sistema</span>
                                    @elseif ($assinatura->ativo)
                                        <span class="badge bg-success">Ativa</span>
                                    @else
                                        <span class="badge bg-secondary">Inativa</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="acoes-cell">
                                        <button type="button" class="btn-icon edit btn-editar-assinatura"
                                                title="Editar"
                                                data-id="{{ $assinatura->id }}"
                                                data-nome="{{ $assinatura->nome }}"
                                                data-conteudo="{{ $assinatura->conteudo }}"
                                                data-ativo="{{ $assinatura->ativo ? '1' : '0' }}"
                                                data-sistema="{{ $assinatura->sistema ? '1' : '0' }}">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        @if (! $assinatura->sistema)
                                            <button type="button" class="btn-icon delete btn-excluir-assinatura"
                                                    title="Excluir"
                                                    data-id="{{ $assinatura->id }}"
                                                    data-nome="{{ $assinatura->nome }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modal Novo Modelo --}}
<div class="modal fade" id="modalNovoModelo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formNovoModelo" action="{{ route('configuracoes.emails.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Novo Modelo de E-mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" class="form-control" required maxlength="150" placeholder="Ex: Boas-vindas ao Locatário">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Escopo *</label>
                            <select name="escopo" class="form-select" id="novoModeloEscopo" required>
                                <option value="">Selecione o escopo</option>
                                @foreach ($escopos as $chave => $nome)
                                    <option value="{{ $chave }}">{{ $nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">E-mail do Remetente *</label>
                            <input type="email" name="remetente_email" class="form-control" required maxlength="150" placeholder="naoresponda@imobiliaria.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nome do Remetente *</label>
                            <input type="text" name="remetente_nome" class="form-control" required maxlength="100" placeholder="Imobiliária">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assunto *</label>
                        <input type="text" name="assunto" class="form-control" required maxlength="255" data-campo-assunto placeholder="Ex: Bem-vindo, @{{nome_locatario}}!">
                        <div class="form-text">Use variáveis como @{{nome_locatario}}, @{{numero_contrato}}, etc.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Corpo do E-mail *</label>
                        @include('configuracoes.emails._editor', ['corpo' => ''])
                        <div class="form-text">Use a barra de ferramentas para formatar. As tags HTML são geradas automaticamente ao salvar.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assinatura</label>
                        <select name="assinatura_email_id" class="form-select">
                            <option value="">Nenhuma</option>
                            @foreach ($assinaturas->where('ativo', true) as $assinatura)
                                <option value="{{ $assinatura->id }}">{{ $assinatura->nome }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Anexada ao final do corpo na visualização e no envio.</div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                            <label class="form-label mb-0">Variáveis do escopo</label>
                            <span id="variaveisBadgeNovo" class="badge bg-info text-dark"></span>
                        </div>
                        <div class="form-text mb-2">Clique no assunto ou no corpo e use <i class="fa-solid fa-plus"></i> para inserir no campo ativo.</div>
                        <div id="variaveisNovo" class="row g-2 email-vars">
                            <p class="text-muted small mb-0 col-12">Selecione o escopo para ver as variáveis disponíveis.</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="ativo" value="1" class="form-check-input" id="novoModeloAtivo" checked>
                        <label class="form-check-label" for="novoModeloAtivo">Modelo ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Nova Assinatura --}}
<div class="modal fade" id="modalNovaAssinatura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('configuracoes.assinaturas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova Assinatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" class="form-control" required maxlength="150" placeholder="Ex: Assinatura Comercial">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conteúdo *</label>
                        @include('configuracoes.emails._editor', ['corpo' => '', 'nomeCampo' => 'conteudo'])
                        <div class="form-text">HTML de assinatura (nome, telefone, endereço, etc.).</div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="ativo" value="1" class="form-check-input" id="novaAssinaturaAtivo" checked>
                        <label class="form-check-label" for="novaAssinaturaAtivo">Assinatura ativa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Assinatura --}}
<div class="modal fade" id="modalEditarAssinatura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditarAssinatura" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Assinatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="editarAssinaturaNome" class="form-control" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Conteúdo *</label>
                        @include('configuracoes.emails._editor', ['corpo' => '', 'nomeCampo' => 'conteudo'])
                        <div class="form-text">HTML de assinatura (nome, telefone, endereço, etc.).</div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="ativo" value="1" class="form-check-input" id="editarAssinaturaAtivo">
                        <label class="form-check-label" for="editarAssinaturaAtivo">Assinatura ativa</label>
                        <div class="form-text d-none" id="editarAssinaturaSistemaHint">Assinaturas de sistema ficam sempre ativas.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Confirmar Exclusão de Assinatura --}}
<div class="modal fade" id="modalExcluirAssinatura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a assinatura <strong id="excluirAssinaturaNome"></strong>?</p>
                <p class="text-muted small">Modelos que a utilizam ficarão sem assinatura.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluirAssinatura" action="" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmar Exclusão --}}
<div class="modal fade" id="modalExcluirModelo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o modelo <strong id="excluirModeloNome"></strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluirModelo" action="" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var destroyUrl = '{{ route('configuracoes.emails.destroy', ':id') }}';
    var destroyAssinaturaUrl = '{{ route('configuracoes.assinaturas.destroy', ':id') }}';
    var updateAssinaturaUrl = '{{ route('configuracoes.assinaturas.update', ':id') }}';
    var variaveisUrl = '{{ route('configuracoes.emails.variaveis') }}';

    document.querySelectorAll('.btn-excluir-modelo').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('excluirModeloNome').textContent = this.dataset.nome;
            document.getElementById('formExcluirModelo').action = destroyUrl.replace(':id', this.dataset.id);
            new bootstrap.Modal(document.getElementById('modalExcluirModelo')).show();
        });
    });

    document.querySelectorAll('.btn-novo-modelo').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var escopo = this.dataset.escopo;
            if (escopo) {
                document.getElementById('novoModeloEscopo').value = escopo;
                carregarVariaveisNovo(escopo);
            }
        });
    });

    function carregarVariaveisNovo(escopo) {
        var container = document.getElementById('variaveisNovo');
        var badge = document.getElementById('variaveisBadgeNovo');
        if (!escopo) {
            container.innerHTML = '<p class="text-muted small mb-0 col-12">Selecione o escopo para ver as variáveis disponíveis.</p>';
            badge.textContent = '';
            return;
        }
        fetch(variaveisUrl + '?escopo=' + encodeURIComponent(escopo))
            .then(function (r) { return r.json(); })
            .then(function (variaveis) {
                renderVariaveisNovo(variaveis);
                badge.textContent = Object.keys(variaveis).length + ' variável(is)';
            })
            .catch(function () {
                container.innerHTML = '<p class="text-danger small mb-0 col-12">Erro ao carregar variáveis.</p>';
            });
    }

    function renderVariaveisNovo(variaveis) {
        var container = document.getElementById('variaveisNovo');
        container.innerHTML = '';
        var chaves = Object.keys(variaveis);
        if (chaves.length === 0) {
            container.innerHTML = '<p class="text-muted small mb-0 col-12">Nenhuma variável pré-definida para este escopo.</p>';
            return;
        }
        chaves.forEach(function (variavel) {
            var div = document.createElement('div');
            div.className = 'col-12 col-md-6 col-lg-4';
            div.innerHTML =
                '<div class="d-flex align-items-center gap-2 p-2 bg-light rounded">' +
                '<div class="flex-grow-1 min-w-0">' +
                '<div class="small fw-semibold text-body text-truncate" title="' + variaveis[variavel].replace(/"/g, '&quot;') + '">' + variaveis[variavel] + '</div>' +
                '<code class="email-vars-chip">' + variavel + '</code>' +
                '</div>' +
                '<button type="button" class="btn btn-sm btn-outline-secondary ms-auto btn-inserir-variavel-novo" data-variavel="' + variavel + '" title="Inserir variável">' +
                '<i class="fa-solid fa-plus"></i>' +
                '</button>' +
                '</div>';
            container.appendChild(div);
        });
        container.querySelectorAll('.btn-inserir-variavel-novo').forEach(function (btn) {
            btn.addEventListener('mousedown', function (e) {
                e.preventDefault();
            });
            btn.addEventListener('click', function () {
                window.inserirVariavelEmail(this.dataset.variavel);
            });
        });
    }

    document.getElementById('novoModeloEscopo').addEventListener('change', function () {
        carregarVariaveisNovo(this.value);
    });

    document.querySelectorAll('.btn-editar-assinatura').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = document.getElementById('formEditarAssinatura');
            form.action = updateAssinaturaUrl.replace(':id', this.dataset.id);
            document.getElementById('editarAssinaturaNome').value = this.dataset.nome;
            document.getElementById('editarAssinaturaAtivo').checked = this.dataset.ativo === '1';
            document.getElementById('editarAssinaturaSistemaHint').classList.toggle('d-none', this.dataset.sistema !== '1');

            var fonte = form.querySelector('[data-email-editor-source]');
            var area = form.querySelector('[data-email-editor-content]');
            if (fonte && area) {
                fonte.value = this.dataset.conteudo;
                area.innerHTML = this.dataset.conteudo || '<p><br></p>';
            }

            new bootstrap.Modal(document.getElementById('modalEditarAssinatura')).show();
        });
    });

    document.querySelectorAll('.btn-excluir-assinatura').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('excluirAssinaturaNome').textContent = this.dataset.nome;
            document.getElementById('formExcluirAssinatura').action = destroyAssinaturaUrl.replace(':id', this.dataset.id);
            new bootstrap.Modal(document.getElementById('modalExcluirAssinatura')).show();
        });
    });
});
</script>
