{{-- Partial: Configurações do Sistema --}}
<form id="formConfiguracoesSistema" action="{{ route('configuracoes.update-multiplo') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        @foreach ($gruposSistema as $grupoChave => $grupoNome)
            @if ($configuracoesPorGrupo->has($grupoChave))
                <div class="col-12 col-lg-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fa-solid fa-tag me-1 text-primary"></i>
                                {{ $grupoNome }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach ($configuracoesPorGrupo[$grupoChave] as $config)
                                @include('configuracoes._campo', ['config' => $config])
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Grupos não mapeados --}}
        @foreach ($configuracoesPorGrupo->keys() as $grupo)
            @if (! isset($gruposSistema[$grupo]))
                <div class="col-12 col-lg-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fa-solid fa-tag me-1 text-secondary"></i>
                                {{ ucfirst($grupo) }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach ($configuracoesPorGrupo[$grupo] as $config)
                                @include('configuracoes._campo', ['config' => $config])
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if ($configuracoesPorGrupo->flatten()->where('sistema', false)->count() > 0)
        <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i> Salvar Alterações
            </button>
        </div>
    @endif
</form>