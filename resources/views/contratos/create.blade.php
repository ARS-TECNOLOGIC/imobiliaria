@extends('layouts.app')

@section('titulo', 'Novo contrato')

@section('conteudo')
    <h1 class="h3 mb-3">Novo contrato</h1>

    <form action="{{ route('contratos.store') }}" method="POST">
        @csrf
        @include('contratos._form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('contratos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
