@extends('layouts.app')

@section('titulo', 'Nova fatura')

@section('conteudo')
    <h1 class="h3 mb-3">Nova fatura</h1>

    <form action="{{ route('faturas.store') }}" method="POST">
        @csrf
        @include('faturas._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('faturas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
