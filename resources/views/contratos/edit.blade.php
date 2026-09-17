@extends('layouts.app')

@section('titulo', 'Editar contrato')

@section('conteudo')
    <h1 class="h3 mb-3">Editar contrato #{{ $contrato->id }}</h1>

    <form action="{{ route('contratos.update', $contrato) }}" method="POST">
        @csrf
        @method('PUT')
        @include('contratos._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('contratos.show', $contrato) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
