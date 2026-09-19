<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Sistema de Locação')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('pessoas.index') }}">
                <span class="brand-icon">S</span>
                Sistema de Locação
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="{{ route('pessoas.index') }}">Pessoas</a>
                    <a class="nav-link" href="{{ route('imoveis.index') }}">Imóveis</a>
                    <a class="nav-link" href="{{ route('contratos.index') }}">Contratos</a>
                    <a class="nav-link" href="{{ route('faturas.index') }}">Faturas</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('sucesso'))
            <div class="alert alert-success ds-animate-slide-down">{{ session('sucesso') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger ds-animate-slide-down">
                <strong>Corrija os seguintes erros:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('conteudo')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
