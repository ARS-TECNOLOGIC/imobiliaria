<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Sistema de Locação')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('pessoas.index') }}">Sistema de Locação</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('pessoas.index') }}">Pessoas</a>
                <a class="nav-link" href="{{ route('imoveis.index') }}">Imóveis</a>
                <a class="nav-link" href="{{ route('contratos.index') }}">Contratos</a>
                <a class="nav-link" href="{{ route('faturas.index') }}">Faturas</a>
                {{-- O link de Corretores entra aqui assim que o controller for criado. --}}
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('sucesso'))
            <div class="alert alert-success">{{ session('sucesso') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Corrija os seguintes erros:</strong>
                <ul class="mb-0">
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
