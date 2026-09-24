@extends('layouts.app')

@section('titulo', 'Editar Modelo de E-mail: ' . $modelo->nome)

@section('conteudo')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Editar Modelo de E-mail</h1>
        <a href="{{ route('configuracoes.index', ['aba' => 'emails']) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if (session('erro'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('erro') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form de update associado via atributo HTML5 form= (permite anexos dentro de Conteúdo sem form aninhado) --}}
    <form id="formUpdateModelo" action="{{ route('configuracoes.emails.update', $modelo) }}" method="POST">
        @csrf
        @method('PUT')
    </form>

    <div class="card mb-4">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-bold">Informações Básicas</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nome *</label>
                    <input type="text" form="formUpdateModelo" name="nome" class="form-control" required maxlength="150" value="{{ $modelo->nome }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Escopo *</label>
                    <select form="formUpdateModelo" name="escopo" class="form-select" id="editEscopo" required>
                        @foreach ($escopos as $chave => $nome)
                            <option value="{{ $chave }}" {{ $modelo->escopo === $chave ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">E-mail do Remetente *</label>
                    <input type="email" form="formUpdateModelo" name="remetente_email" class="form-control" required maxlength="150" value="{{ $modelo->remetente_email }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome do Remetente *</label>
                    <input type="text" form="formUpdateModelo" name="remetente_nome" class="form-control" required maxlength="100" value="{{ $modelo->remetente_nome }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Conteúdo do E-mail</h6>
            <div id="variaveisBadge" class="badge bg-info text-dark"></div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Assunto *</label>
                <input type="text" form="formUpdateModelo" name="assunto" class="form-control" required maxlength="255" value="{{ $modelo->assunto }}" data-campo-assunto>
                <div class="form-text">Use variáveis como @{{nome_locatario}}, @{{numero_contrato}}, etc.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Corpo do E-mail *</label>
                @include('configuracoes.emails._editor', ['corpo' => $modelo->corpo, 'formId' => 'formUpdateModelo'])
                <div class="form-text">Use a barra de ferramentas para formatar. As tags HTML são geradas automaticamente ao salvar.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Assinatura</label>
                <select form="formUpdateModelo" name="assinatura_email_id" class="form-select">
                    <option value="">Nenhuma</option>
                    @foreach ($assinaturas as $assinatura)
                        @if ($assinatura->ativo || $modelo->assinatura_email_id === $assinatura->id)
                            <option value="{{ $assinatura->id }}" {{ $modelo->assinatura_email_id === $assinatura->id ? 'selected' : '' }}>
                                {{ $assinatura->nome }}{{ $assinatura->ativo ? '' : ' (inativa)' }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <div class="form-text">Anexada ao final do corpo na visualização e no envio.</div>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-3"><i class="fa-solid fa-paperclip me-1"></i> Anexos</h6>
            <form action="{{ route('configuracoes.emails.anexos.store', $modelo) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="input-group">
                    <input type="file" name="arquivo" class="form-control" required
                           accept=".pdf,.jpeg,.jpg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.zip,.txt">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa-solid fa-paperclip me-1"></i> Anexar
                    </button>
                </div>
                <div class="form-text">PDF, imagens, Office, ZIP ou TXT. Até 10 MB.</div>
            </form>

            @if ($modelo->anexos->isEmpty())
                <p class="text-muted small mb-0">Nenhum anexo neste modelo.</p>
            @else
                <ul class="list-group">
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
                            <form action="{{ route('configuracoes.emails.anexos.destroy', [$modelo, $anexo]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon delete" title="Remover anexo">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">Variáveis Disponíveis para o Escopo Selecionado</h6>
        </div>
        <div class="card-body">
            <div class="form-text mb-2">Clique no assunto ou no corpo e use <i class="fa-solid fa-plus"></i> para inserir no campo ativo.</div>
            <div id="variaveisDisponiveis" class="row g-2 email-vars">
                @foreach ($variaveisEscopo as $variavel => $descricao)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                            <div class="flex-grow-1 min-w-0">
                                <div class="small fw-semibold text-body text-truncate" title="{{ $descricao }}">{{ $descricao }}</div>
                                <code class="email-vars-chip">{{ $variavel }}</code>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto btn-inserir-variavel" data-variavel="{{ $variavel }}" title="Inserir variável">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            @if (empty($variaveisEscopo))
                <p class="text-muted mb-0">Nenhuma variável pré-definida para este escopo.</p>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-bold">Opções</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="checkbox" form="formUpdateModelo" name="ativo" value="1" class="form-check-input" id="ativoCheck" {{ $modelo->ativo ? 'checked' : '' }} {{ $modelo->sistema ? 'disabled' : '' }}>
                        <label class="form-check-label" for="ativoCheck">Modelo ativo</label>
                    </div>
                    @if ($modelo->sistema)
                        <div class="form-text text-muted">Modelos de sistema não podem ser desativados.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Descrição</label>
                    <textarea form="formUpdateModelo" name="descricao" class="form-control" rows="2" maxlength="255">{{ $modelo->descricao }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('configuracoes.index', ['aba' => 'emails']) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" form="formUpdateModelo" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk me-1"></i> Salvar Alterações
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const escopoSelect = document.getElementById('editEscopo');
    const variaveisContainer = document.getElementById('variaveisDisponiveis');
    const variaveisBadge = document.getElementById('variaveisBadge');

    async function carregarVariaveis(escopo) {
        try {
            const response = await fetch('{{ route('configuracoes.emails.variaveis') }}?escopo=' + encodeURIComponent(escopo));
            const data = await response.json();
            renderVariaveis(data);
            variaveisBadge.textContent = Object.keys(data).length + ' variável(is)';
        } catch (e) {
            console.error('Erro ao carregar variáveis:', e);
        }
    }

    function renderVariaveis(variaveis) {
        variaveisContainer.innerHTML = '';
        if (Object.keys(variaveis).length === 0) {
            variaveisContainer.innerHTML = '<p class="text-muted mb-0">Nenhuma variável pré-definida para este escopo.</p>';
            return;
        }
        for (const [variavel, descricao] of Object.entries(variaveis)) {
            const div = document.createElement('div');
            div.className = 'col-12 col-md-6 col-lg-4';
            div.innerHTML = `
                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                    <div class="flex-grow-1 min-w-0">
                        <div class="small fw-semibold text-body text-truncate" title="${descricao.replace(/"/g, '&quot;')}">${descricao}</div>
                        <code class="email-vars-chip">${variavel}</code>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto btn-inserir-variavel" data-variavel="${variavel}" title="Inserir variável">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            `;
            variaveisContainer.appendChild(div);
        }
        document.querySelectorAll('.btn-inserir-variavel').forEach(btn => {
            btn.addEventListener('mousedown', e => e.preventDefault());
            btn.addEventListener('click', function () {
                window.inserirVariavelEmail(this.dataset.variavel);
            });
        });
    }

    escopoSelect?.addEventListener('change', function () {
        carregarVariaveis(this.value);
    });

    carregarVariaveis('{{ $modelo->escopo }}');
});
</script>
@endsection
