@extends('layouts.app')

@section('tito', 'Nova pessoa')

@section('conteudo')
    <h1 class="h3 mb-3">Nova pessoa</h1>

    <form action="{{ route('pessoas.store') }}" method="POST">
        @csrf
        @include('pessoas._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>

    @include('pessoas._modal_nova_pessoa')
@endsection

@section('scripts')
@php
    $estadosVinculo = [\App\Enums\EstadoCivil::CASADO, \App\Enums\EstadoCivil::UNIAO_ESTAVEL];
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    var estadoCivil = document.getElementById('estado-civil');
    var secaoVinculo = document.getElementById('secao-vinculo');
    var conjugeSelect = document.getElementById('conjuge-select');
    var formModal = document.getElementById('form-modal-pessoa');
    var modalErros = document.getElementById('modal-erros');
    var btnSalvar = document.getElementById('btn-modal-salvar');
    var modalEl = document.getElementById('modalNovaPessoa');

    if (!estadoCivil || !secaoVinculo || !conjugeSelect || !formModal || !modalEl) return;

    var estadosComVinculo = @json(array_column($estadosVinculo, 'value'));

    function toggleSecaoVinculo() {
        secaoVinculo.style.display = estadosComVinculo.indexOf(estadoCivil.value) !== -1 ? '' : 'none';
    }

    estadoCivil.addEventListener('change', toggleSecaoVinculo);
    toggleSecaoVinculo();

    var pessoasCarregadas = false;

    function carregarPessoas() {
        var params = new URLSearchParams();

        return fetch('{{ route("pessoas.buscar") }}?' + params.toString())
            .then(function (resp) { return resp.json(); })
            .then(function (dados) {
                var valorAntigo = conjugeSelect.value;
                conjugeSelect.innerHTML = '<option value="">— Selecione uma pessoa —</option>';

                dados.forEach(function (p) {
                    var opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nome + ' (' + p.cpf_cnpj + ')';
                    conjugeSelect.appendChild(opt);
                });

                if (valorAntigo) {
                    conjugeSelect.value = valorAntigo;
                }

                pessoasCarregadas = true;
            });
    }

    conjugeSelect.addEventListener('focus', function () {
        if (!pessoasCarregadas) {
            carregarPessoas();
        }
    });

    formModal.addEventListener('submit', function (e) {
        e.preventDefault();
        modalErros.classList.add('d-none');
        modalErros.innerHTML = '';
        btnSalvar.disabled = true;

        var formData = new FormData(formModal);
        formData.append('estado_civil', estadoCivil.value);
        formData.append('tipo_conta', 'corrente');

        var camposPrincipais = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
            'tipo_chave_pix', 'chave_pix', 'banco', 'agencia', 'conta'];
        camposPrincipais.forEach(function (campo) {
            var el = document.querySelector('[name="' + campo + '"]');
            if (el && el.value && !formData.has(campo)) {
                formData.append(campo, el.value);
            }
        });

        fetch('{{ route("pessoas.ajax") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function (resp) {
            return resp.json().then(function (data) { return { ok: resp.ok, data: data }; });
        })
        .then(function (result) {
            if (!result.ok) {
                if (result.data.errors) {
                    var msgs = Object.values(result.data.errors).flat();
                    modalErros.innerHTML = msgs.map(function (m) { return '<div>' + m + '</div>'; }).join('');
                    modalErros.classList.remove('d-none');
                }
                btnSalvar.disabled = false;
                return;
            }

            var opt = document.createElement('option');
            opt.value = result.data.id;
            opt.textContent = result.data.nome + ' (' + result.data.cpf_cnpj + ')';
            conjugeSelect.appendChild(opt);
            conjugeSelect.value = result.data.id;

            formModal.reset();
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        })
        .catch(function () {
            modalErros.innerHTML = '<div>Erro ao salvar. Tente novamente.</div>';
            modalErros.classList.remove('d-none');
            btnSalvar.disabled = false;
        });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        formModal.reset();
        modalErros.classList.add('d-none');
        modalErros.innerHTML = '';
        btnSalvar.disabled = false;
    });
});
</script>
@endsection
