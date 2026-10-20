{{-- Mensagens de retorno das ações (sucesso/erro). --}}
@if (session('status'))
    <p class="alert alert--success" role="status"><x-icon name="check" /> {{ session('status') }}</p>
@endif
@if ($errors->any())
    <div class="alert alert--error" role="alert">
        <x-icon name="warning" />
        <span>Não foi possível salvar. Revise os campos destacados.</span>
    </div>
@endif
