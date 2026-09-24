@extends('layouts.app')

@section('titulo', 'Visualizar Modelo de E-mail: ' . $modelo->nome)

@section('conteudo')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $modelo->nome }}</h1>
            <p class="text-muted mb-0">{{ \App\Models\ModeloEmail::escoposDisponiveis()[$modelo->escopo] ?? $modelo->escopo }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('configuracoes.emails.edit', $modelo) }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-pencil me-1"></i> Editar
            </a>
            @if (! $modelo->sistema)
                <button type="button" class="btn btn-outline-danger btn-excluir-modelo" data-id="{{ $modelo->id }}" data-nome="{{ $modelo->nome }}">
                    <i class="fa-solid fa-trash me-1"></i> Excluir
                </button>
            @endif
            <a href="{{ route('configuracoes.index', ['aba' => 'emails']) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Informações do Modelo --}}
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 fw-bold">Informações</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted small">Status</dt>
                        <dd class="col-sm-8">
                            @if ($modelo->sistema)
                                <span class="badge bg-primary">Sistema</span>
                            @elseif ($modelo->ativo)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted small">Slug</dt>
                        <dd class="col-sm-8"><code>{{ $modelo->slug }}</code></dd>

                        <dt class="col-sm-4 text-muted small">Remetente</dt>
                        <dd class="col-sm-8">{{ $modelo->remetente_nome }} <{{ $modelo->remetente_email }}></dd>

                        <dt class="col-sm-4 text-muted small">Assinatura</dt>
                        <dd class="col-sm-8">
                            @if ($modelo->assinatura)
                                {{ $modelo->assinatura->nome }}
                                @if (! $modelo->assinatura->ativo)
                                    <span class="badge bg-secondary">Inativa</span>
                                @endif
                            @else
                                <span class="text-muted">Nenhuma</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4 text-muted small">Criado em</dt>
                        <dd class="col-sm-8">{{ $modelo->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4 text-muted small">Atualizado em</dt>
                        <dd class="col-sm-8">{{ $modelo->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            @if ($modelo->descricao)
                <div class="card mt-3">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold">Descrição</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $modelo->descricao }}</p>
                    </div>
                </div>
            @endif

            @if ($modelo->anexos->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold">Anexos</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($modelo->anexos as $anexo)
                            <li class="list-group-item d-flex align-items-center gap-2">
                                <i class="fa-solid fa-file-lines text-muted"></i>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="text-truncate" title="{{ $anexo->nome_original }}">{{ $anexo->nome_original }}</div>
                                    <small class="text-muted">{{ $anexo->tamanhoFormatado() }}</small>
                                </div>
                                <a href="{{ $anexo->urlDownload() }}" class="btn-icon view" title="Baixar">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Preview do E-mail --}}
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Preview do E-mail</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="copiarAssunto()">
                            <i class="fa-regular fa-clipboard me-1"></i> Copiar Assunto
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="copiarCorpo()">
                            <i class="fa-regular fa-clipboard me-1"></i> Copiar Corpo
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <strong class="text-muted small d-block mb-1">Assunto:</strong>
                        <div id="previewAssunto" class="fw-medium">{{ $modelo->assunto }}</div>
                    </div>

                    <div class="border rounded overflow-hidden" style="min-height: 300px;">
                        <div class="bg-white p-3" id="previewCorpo">
                            {!! $modelo->corpoComAssinatura() !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Variáveis Disponíveis --}}
            <div class="card mt-4">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 fw-bold">Variáveis Disponíveis para este Escopo</h6>
                </div>
                <div class="card-body">
                    @if (!empty($variaveisEscopo))
                        <div class="row g-2">
                            @foreach ($variaveisEscopo as $variavel => $descricao)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="small fw-semibold text-body text-truncate" title="{{ $descricao }}">{{ $descricao }}</div>
                                            <code class="email-vars-chip">{{ $variavel }}</code>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Nenhuma variável pré-definida para este escopo.</p>
                    @endif
                </div>
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
@endsection

@section('scripts')
<script>
function copiarAssunto() {
    navigator.clipboard.writeText(document.getElementById('previewAssunto').innerText);
    showToast('Assunto copiado!', 'success');
}

function copiarCorpo() {
    navigator.clipboard.writeText(document.getElementById('previewCorpo').innerHTML);
    showToast('Corpo copiado!', 'success');
}

function showToast(msg, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-' + type + ' border-0 position-fixed bottom-0 end-0 m-3';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = '<div class="d-flex"><div class="toast-body">' + msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    document.body.appendChild(toast);
    new bootstrap.Toast(toast).show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

document.addEventListener('DOMContentLoaded', function () {
    // Excluir
    document.querySelectorAll('.btn-excluir-modelo').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('excluirModeloNome').textContent = this.dataset.nome;
            document.getElementById('formExcluirModelo').action = '{{ route('configuracoes.emails.destroy', ':id') }}'.replace(':id', this.dataset.id);
            new bootstrap.Modal(document.getElementById('modalExcluirModelo')).show();
        });
    });
});
</script>
@endsection