<div class="offcanvas offcanvas-start shell-sidebar--mobile" tabindex="-1" id="shellSidebar"
     aria-labelledby="shellSidebarLabel">
    <div class="offcanvas-header shell-sidebar__mobile-header">
        <span class="shell-topbar__logo">SL</span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body shell-sidebar__mobile-body">
        <nav class="shell-sidebar__nav">
            <a href="{{ route('pessoas.index') }}"
               class="shell-sidebar__item {{ request()->routeIs('pessoas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users shell-sidebar__icon"></i>
                <span>Pessoas</span>
            </a>
            <a href="{{ route('imoveis.index') }}"
               class="shell-sidebar__item {{ request()->routeIs('imoveis.*') ? 'active' : '' }}">
                <i class="fa-regular fa-building shell-sidebar__icon"></i>
                <span>Im&oacute;veis</span>
            </a>
            <a href="{{ route('contratos.index') }}"
               class="shell-sidebar__item {{ request()->routeIs('contratos.*') ? 'active' : '' }}">
                <i class="fa-regular fa-file-lines shell-sidebar__icon"></i>
                <span>Contratos</span>
            </a>
            <a href="{{ route('faturas.index') }}"
               class="shell-sidebar__item {{ request()->routeIs('faturas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice shell-sidebar__icon"></i>
                <span>Faturas</span>
            </a>
            <a href="{{ route('configuracoes.index') }}"
               class="shell-sidebar__item {{ request()->routeIs('configuracoes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear shell-sidebar__icon"></i>
                <span>Configura&ccedil;&otilde;es</span>
            </a>
        </nav>
    </div>
</div>
