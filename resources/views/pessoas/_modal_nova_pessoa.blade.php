<div class="modal fade" id="modalNovaPessoa" tabindex="-1" aria-labelledby="modalNovaPessoaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovaPessoaLabel">Nova pessoa (cônjuge)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="form-modal-pessoa" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" class="form-control" required maxlength="150">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CPF/CNPJ *</label>
                            <input type="text" name="cpf_cnpj" class="form-control" required maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" maxlength="150">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" class="form-control" maxlength="20">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-danger d-none" id="modal-erros"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-modal-salvar">
                        <i class="fa-solid fa-check me-1"></i> Salvar e selecionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
