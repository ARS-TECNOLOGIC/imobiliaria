<header class="shell-topbar">
    <div class="shell-topbar__left">
        <button class="shell-topbar__toggle d-lg-none" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#shellSidebar"
                aria-controls="shellSidebar" aria-label="Abrir menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="{{ route('contratos.index') }}" class="shell-topbar__brand">
            <span class="shell-topbar__logo">SL</span>
            <span class="shell-topbar__title">Sistema de Loca&ccedil;&atilde;o</span>
        </a>
    </div>
    <div class="shell-topbar__right">
        {{-- area reservada: busca, notificacoes e menu do usuario --}}
    </div>
</header>
