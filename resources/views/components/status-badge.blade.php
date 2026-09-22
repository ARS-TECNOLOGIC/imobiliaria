@props(['status'])

@php
    $map = [
        'ATIVO'           => ['class' => 'ativo',           'label' => 'Ativo'],
        'ENCERRADO'       => ['class' => 'encerrado',       'label' => 'Encerrado'],
        'SUSPENSO'        => ['class' => 'suspenso',        'label' => 'Suspenso'],
        'PENDENTE'        => ['class' => 'pendente',        'label' => 'Pendente'],
        'PAGO'            => ['class' => 'pago',            'label' => 'Pago'],
        'PAGO_COM_ATRASO' => ['class' => 'pago-com-atraso', 'label' => 'Pago com atraso'],
        'ATRASADO'        => ['class' => 'atrasado',        'label' => 'Atrasado'],
        'CANCELADO'       => ['class' => 'cancelado',       'label' => 'Cancelado'],
        'A_RENOVAR'       => ['class' => 'pendente',        'label' => 'A renovar'],
        'EXPIRADO'        => ['class' => 'encerrado',       'label' => 'Expirado'],
    ];
    $info = $map[$status->value] ?? [
        'class' => 'pendente',
        'label' => str_replace('_', ' ', $status->value),
    ];
@endphp

<span class="badge-status {{ $info['class'] }}">
    <i class="fa-solid fa-circle"></i>
    {{ $info['label'] }}
</span>
