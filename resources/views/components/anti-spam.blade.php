{{--
    Proteção anti-spam do formulário público:
    - honeypot: campo invisível para pessoas, que robôs costumam preencher;
    - armadilha de tempo: instante de carregamento, criptografado;
    - Cloudflare Turnstile, quando as chaves estiverem configuradas.
--}}
<div class="campo-armadilha" aria-hidden="true">
    <label>Deixe este campo em branco
        <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
    </label>
</div>
<input type="hidden" name="_carregado_em" value="{{ \App\Rules\EnvioHumano::marcarAgora() }}">

@if (\App\Support\Turnstile::ativo())
    @once
        @push('scripts')
            <script src="{{ \App\Support\Turnstile::SCRIPT }}" nonce="{{ Vite::cspNonce() }}" async defer></script>
        @endpush
    @endonce
    <div class="turnstile">
        <div class="cf-turnstile" data-sitekey="{{ \App\Support\Turnstile::chaveDoSite() }}" data-language="pt-br" data-theme="light"></div>
        @error(\App\Support\Turnstile::CAMPO)
            <span class="field__error">{{ $message }}</span>
        @enderror
    </div>
@endif

@error('website')
    <span class="field__error">{{ $message }}</span>
@enderror
@error('_carregado_em')
    <span class="field__error">{{ $message }}</span>
@enderror
