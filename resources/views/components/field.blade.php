{{-- Campo de formulário com rótulo, dica e mensagem de validação. --}}
@props(['name', 'label', 'hint' => null, 'optional' => false])
<label {{ $attributes->class(['field', 'field--invalid' => $errors->has($name)]) }}>
    <span class="field__label">
        {{ $label }}
        @if ($optional)
            <span class="field__optional">(opcional)</span>
        @endif
    </span>
    {{ $slot }}
    @if ($hint)
        <span class="field__hint">{{ $hint }}</span>
    @endif
    @error($name)
        <span class="field__error">{{ $message }}</span>
    @enderror
</label>
