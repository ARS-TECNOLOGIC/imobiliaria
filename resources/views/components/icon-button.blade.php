@props(['variant' => 'view', 'href' => null, 'icon' => null, 'title' => ''])

@php
    $iconMap = [
        'view'   => 'fa-regular fa-eye',
        'edit'   => 'fa-solid fa-pencil',
        'delete' => 'fa-solid fa-trash',
    ];
    $tag = $href ? 'a' : 'button';
    $defaultIcon = $iconMap[$variant] ?? 'fa-solid fa-circle';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    class="btn-icon {{ $variant }}"
    @if($tag === 'button') type="button" @endif
    @if($title) title="{{ $title }}" @endif
    {{ $attributes }}
>
    <i class="{{ $icon ?? $defaultIcon }}"></i>
</{{ $tag }}>
