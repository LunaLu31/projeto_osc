@props(['href' => route('feed'), 'subtitulo' => 'Rede que transforma'])
<a href="{{ $href }}" {{ $attributes->class('logo') }} aria-label="Redcam – {{ $subtitulo }}">
    <span class="logo__mark"><x-icon name="heart" /></span>
    <span class="logo__copy">
        <strong>Redcam</strong>
        <small>{{ $subtitulo }}</small>
    </span>
</a>
