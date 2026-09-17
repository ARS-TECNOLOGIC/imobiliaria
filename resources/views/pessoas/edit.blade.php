@extends('layouts.app')

@section('titulo', 'Editar pessoa')

@section('conteudo')
    <h1 class="h3 mb-3">Editar pessoa</h1>

    <form action="{{ route('pessoas.update', $pessoa) }}" method="POST">
        @csrf
        @method('PUT')
        @include('pessoas._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
