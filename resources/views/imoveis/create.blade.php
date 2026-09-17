@extends('layouts.app')

@section('titulo', 'Novo imóvel')

@section('conteudo')
    <h1 class="h3 mb-3">Novo imóvel</h1>

    <form action="{{ route('imoveis.store') }}" method="POST">
        @csrf
        @include('imoveis._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('imoveis.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
