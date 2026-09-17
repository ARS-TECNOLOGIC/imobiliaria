@extends('layouts.app')

@section('titulo', 'Editar fatura')

@section('conteudo')
    <h1 class="h3 mb-3">Editar fatura #{{ $fatura->id }}</h1>

    <form action="{{ route('faturas.update', $fatura) }}" method="POST">
        @csrf
        @method('PUT')
        @include('faturas._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('faturas.show', $fatura) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
