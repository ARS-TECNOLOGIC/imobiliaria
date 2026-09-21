@props(['status'])

@php
    $map = [
        'ATIVO'     => ['class' => 'ativo',     'label' => 'Ativo'],
        'ENCERRADO' => ['class' => 'encerrado', 'label' => 'Encerrado'],
        'SUSPENSO'  => ['class' => 'pendente',  'label' => 'Pendente'],
    ];
    $info = $map[$status->value] ?? ['class' => 'pendente', 'label' => $status->value];
@endphp

<span class="badge-status {{ $info['class'] }}">
    <i class="fa-solid fa-circle"></i>
    {{ $info['label'] }}
</span>
