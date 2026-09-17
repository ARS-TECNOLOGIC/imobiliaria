@extends('layouts.app')

@section('titulo', 'Nova pessoa')

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
@endsection
