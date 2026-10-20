@props(['status'])
@php
    $modificador = match ($status) {
        \App\Models\Demanda::STATUS_ABERTA => 'badge--aberta',
        \App\Models\Demanda::STATUS_PARCIAL => 'badge--parcial',
        \App\Models\Demanda::STATUS_CONCLUIDA => 'badge--concluida',
        default => null,
    };
@endphp
<span {{ $attributes->class(['badge', $modificador]) }}>{{ \App\Models\Demanda::STATUS[$status] ?? $status }}</span>
