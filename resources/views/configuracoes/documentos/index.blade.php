{{-- Pastas de Documentos --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 mb-0">Pastas de Documentos</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaPasta">
        <i class="fa-solid fa-plus me-1"></i> Nova Pasta
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelaPastas">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Ordem</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Descrição</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pastas as $pasta)
                        <tr data-id="{{ $pasta->id }}">
                            <td>
                                <input type="number" class="form-control form-control-sm ordem-pasta" value="{{ $pasta->ordem }}" min="0" {{ $pasta->sistema ? 'disabled' : '' }}>
                            </td>
                            <td class="fw-medium">{{ $pasta->nome }}</td>
                            <td><code class="text-muted">{{ $pasta->slug }}</code></td>
                            <td class="text-muted">{{ $pasta->descricao ?? '-' }}</td>
                            <td>
                                @if ($pasta->sistema)
                                    <span class="badge bg-primary">Sistema</span>
                                @elseif ($pasta->ativa)
                                    <span class="badge bg-success">Ativa</span>
                                @else
                                    <span class="badge bg-secondary">Inativa</span>
                                @endif
                            </td>
                            <td>
                                <div class="acoes-cell">
                                    <button type="button" class="btn-icon edit btn-editar-pasta"
                                            title="Editar"
                                            data-id="{{ $pasta->id }}"
                                            data-nome="{{ $pasta->nome }}"
                                            data-descricao="{{ $pasta->descricao }}"
                                            data-ordem="{{ $pasta->ordem }}"
                                            data-ativa="{{ $pasta->ativa ? '1' : '0' }}"
                                            {{ $pasta->sistema ? 'disabled title="Pasta de sistema não pode ser editada"' : '' }}>
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn-icon delete btn-excluir-pasta"
                                            title="Excluir"
                                            data-id="{{ $pasta->id }}"
                                            data-nome="{{ $pasta->nome }}"
                                            {{ $pasta->sistema ? 'disabled title="Pasta de sistema não pode ser excluída"' : '' }}>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-outline-primary" id="btnSalvarOrdem">
                <i class="fa-solid fa-arrows-up-down me-1"></i> Salvar Ordem
            </button>
        </div>
    </div>
</div>

{{-- Modal Nova Pasta --}}
<div class="modal fade" id="modalNovaPasta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formNovaPasta" action="{{ route('configuracoes.documentos.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova Pasta de Documentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" class="form-control" required maxlength="100" placeholder="Ex: Contratos, Vistorias, Financeiro">
                        <div class="form-text">Nome amigável da pasta.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="2" maxlength="255" placeholder="Descrição opcional da pasta"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" class="form-control" min="0" value="{{ $pastas->count() + 1 }}">
                        <div class="form-text">Ordem de exibição (menor aparece primeiro).</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="ativa" value="1" class="form-check-input" id="ativaCheck" checked>
                        <label class="form-check-label" for="ativaCheck">Pasta ativa</label>
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

{{-- Modal Editar Pasta --}}
<div class="modal fade" id="modalEditarPasta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarPasta" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Pasta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="editarPastaNome" class="form-control" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" id="editarPastaDescricao" class="form-control" rows="2" maxlength="255"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" id="editarPastaOrdem" class="form-control" min="0">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="ativa" value="1" class="form-check-input" id="editarPastaAtiva">
                        <label class="form-check-label" for="editarPastaAtiva">Pasta ativa</label>
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

{{-- Modal Confirmar Exclusão --}}
<div class="modal fade" id="modalExcluirPasta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a pasta <strong id="excluirPastaNome"></strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita. Pastas com documentos vinculados não podem ser excluídas.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluirPasta" action="" method="POST" style="display:inline;">
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
    var updateUrl = '{{ route('configuracoes.documentos.update', ':id') }}';
    var destroyUrl = '{{ route('configuracoes.documentos.destroy', ':id') }}';
    var reorderUrl = '{{ route('configuracoes.documentos.reordenar') }}';
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-editar-pasta').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editarPastaId')?.remove();
            document.getElementById('editarPastaNome').value = this.dataset.nome || '';
            document.getElementById('editarPastaDescricao').value = this.dataset.descricao || '';
            document.getElementById('editarPastaOrdem').value = this.dataset.ordem || '';
            document.getElementById('editarPastaAtiva').checked = this.dataset.ativa === '1';
            document.getElementById('formEditarPasta').action = updateUrl.replace(':id', this.dataset.id);
            new bootstrap.Modal(document.getElementById('modalEditarPasta')).show();
        });
    });

    document.querySelectorAll('.btn-excluir-pasta').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('excluirPastaNome').textContent = this.dataset.nome;
            document.getElementById('formExcluirPasta').action = destroyUrl.replace(':id', this.dataset.id);
            new bootstrap.Modal(document.getElementById('modalExcluirPasta')).show();
        });
    });

    document.getElementById('btnSalvarOrdem').addEventListener('click', function () {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = reorderUrl;

        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = csrfToken;
        form.appendChild(csrf);

        document.querySelectorAll('#tabelaPastas tbody tr').forEach(function (row) {
            var input = row.querySelector('.ordem-pasta');
            if (input) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'ordem[' + row.dataset.id + ']';
                hidden.value = input.value;
                form.appendChild(hidden);
            }
        });

        document.body.appendChild(form);
        form.submit();
    });

    function reordenarLinhas() {
        var tbody = document.querySelector('#tabelaPastas tbody');
        var rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort(function (a, b) {
            var ordemA = parseInt(a.querySelector('.ordem-pasta').value, 10) || 0;
            var ordemB = parseInt(b.querySelector('.ordem-pasta').value, 10) || 0;
            if (ordemA === ordemB) {
                return parseInt(a.dataset.id, 10) - parseInt(b.dataset.id, 10);
            }
            return ordemA - ordemB;
        });
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });
    }

    document.querySelectorAll('.ordem-pasta').forEach(function (input) {
        input.addEventListener('change', function () {
            var rowAlterada = this.closest('tr');
            var novaOrdem = parseInt(this.value, 10) || 0;
            var ordemAnterior = parseInt(this.dataset.ordemOriginal, 10);

            if (!isNaN(ordemAnterior) && ordemAnterior !== novaOrdem) {
                document.querySelectorAll('#tabelaPastas tbody tr').forEach(function (row) {
                    if (row === rowAlterada) {
                        return;
                    }
                    var inputOutro = row.querySelector('.ordem-pasta');
                    var ordemOutro = parseInt(inputOutro.value, 10) || 0;
                    if (ordemOutro === novaOrdem) {
                        inputOutro.value = ordemAnterior;
                        inputOutro.dataset.ordemOriginal = ordemAnterior;
                    }
                });
            }

            this.dataset.ordemOriginal = novaOrdem;
            reordenarLinhas();
        });
    });

    document.querySelectorAll('.ordem-pasta').forEach(function (input) {
        input.dataset.ordemOriginal = input.value;
    });
});
</script>