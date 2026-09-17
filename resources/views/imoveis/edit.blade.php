@extends('layouts.app')

@section('titulo', 'Editar imóvel')

@section('conteudo')
    <h1 class="h3 mb-3">Editar imóvel</h1>

    <form action="{{ route('imoveis.update', $imovel) }}" method="POST">
        @csrf
        @method('PUT')
        @include('imoveis._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('imoveis.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
