@extends('layouts.app')

@section('titulo', 'Configurações do Sistema')

@section('conteudo')
@php
    $abaAtiva = in_array(request('aba'), ['sistema', 'documentos', 'emails'], true)
        ? request('aba')
        : session('aba', 'sistema');
@endphp
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Configurações do Sistema</h1>
    </div>

    @if (session('erro'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('erro') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $abaAtiva === 'sistema' ? 'active' : '' }}" id="tab-sistema" data-bs-toggle="tab" data-bs-target="#panel-sistema" type="button" role="tab">
                <i class="fa-solid fa-gear me-1"></i> Sistema
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $abaAtiva === 'documentos' ? 'active' : '' }}" id="tab-documentos" data-bs-toggle="tab" data-bs-target="#panel-documentos" type="button" role="tab">
                <i class="fa-solid fa-folder me-1"></i> Documentos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $abaAtiva === 'emails' ? 'active' : '' }}" id="tab-emails" data-bs-toggle="tab" data-bs-target="#panel-emails" type="button" role="tab">
                <i class="fa-solid fa-envelope me-1"></i> E-mails
            </button>
        </li>
    </ul>

    <div class="tab-content" id="configTabsContent">
        {{-- ABA SISTEMA --}}
        <div class="tab-pane fade {{ $abaAtiva === 'sistema' ? 'show active' : '' }}" id="panel-sistema" role="tabpanel">
            @include('configuracoes._sistema')
        </div>

        {{-- ABA DOCUMENTOS --}}
        <div class="tab-pane fade {{ $abaAtiva === 'documentos' ? 'show active' : '' }}" id="panel-documentos" role="tabpanel">
            @include('configuracoes.documentos.index')
        </div>

        {{-- ABA E-MAILS --}}
        <div class="tab-pane fade {{ $abaAtiva === 'emails' ? 'show active' : '' }}" id="panel-emails" role="tabpanel">
            @include('configuracoes.emails.index')
        </div>
    </div>
</div>
@endsection