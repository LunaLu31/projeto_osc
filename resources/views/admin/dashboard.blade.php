<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Painel administrativo | REDECAM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind = { config: { theme: { extend: { colors: { ink: '#15312f', muted: '#617572', primary: '#126b62', 'primary-dark': '#0a4f49', 'primary-light': '#dff3ed', canvas: '#f6f8f5', line: '#dfe8e4', coral: '#e55f46', 'blue-light': '#eaf2ff' }, fontFamily: { sans: ['DM Sans', 'sans-serif'], display: ['Manrope', 'sans-serif'] } } } } };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-canvas font-sans text-ink">
    <header class="sticky top-0 z-20 flex min-h-[74px] items-center justify-between gap-4 border-b border-line bg-white/95 px-5 backdrop-blur sm:px-[5vw]">
        <a href="{{ url('/') }}" class="flex items-center gap-3 text-inherit no-underline">
            <span class="grid size-10 place-items-center rounded-[14px_14px_14px_5px] bg-primary font-display font-extrabold text-white">R</span>
            <span><strong class="block font-display text-sm tracking-[0.08em]">REDECAM</strong><small class="mt-0.5 block text-[11px] text-muted">Gestão da rede</small></span>
        </a>
        <div class="flex items-center gap-3">
            <div class="hidden text-right text-xs sm:block"><strong>{{ $admin->nome }}</strong><small class="mt-1 block text-muted">{{ $admin->email }}</small></div>
            <form method="POST" action="{{ url('/admin/logout') }}">@csrf<button type="submit" class="min-h-10 rounded-lg border border-line bg-white px-3 text-xs font-bold transition hover:border-[#aabdb7]">Sair</button></form>
        </div>
    </header>

    <main class="mx-auto w-[min(1180px,calc(100%-32px))] py-8 sm:w-[min(1180px,calc(100%-48px))] sm:py-12">
        <section class="mb-7">
            <span class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-primary">Painel administrativo</span>
            <h1 class="mt-2 font-display text-[29px] font-bold leading-tight sm:text-[36px]">Olá, {{ $admin->nome }}</h1>
            <p class="mt-2 text-sm text-muted">Acompanhe as OSCs e os projetos ligados à sua conta.</p>
        </section>

        <section class="grid grid-cols-1 border border-line bg-white sm:grid-cols-3" aria-label="Resumo da conta">
            <article class="border-b border-line px-5 py-4 sm:border-b-0 sm:border-r sm:px-6">
                <strong class="font-display text-2xl text-primary">{{ $oscs->count() }}</strong><span class="mt-1 block text-xs text-muted">OSCs cadastradas por esta conta</span>
            </article>
            <article class="border-b border-line px-5 py-4 sm:border-b-0 sm:border-r sm:px-6">
                <strong class="font-display text-2xl text-primary">{{ $demandas->count() }}</strong><span class="mt-1 block text-xs text-muted">Projetos e demandas vinculados</span>
            </article>
            <article class="px-5 py-4 sm:px-6">
                <strong class="font-display text-2xl text-primary">{{ $demandas->sum('manifestacoes_count') }}</strong><span class="mt-1 block text-xs text-muted">Manifestações de interesse</span>
            </article>
        </section>

        @if (session('status')) <p class="mt-5 rounded-lg bg-primary-light px-4 py-3 text-sm font-semibold text-primary" role="status">{{ session('status') }}</p> @endif

        <div class="mt-6 grid items-start gap-5 lg:grid-cols-[0.88fr_1.12fr]">
            <section class="min-w-0 border border-line bg-white p-5 sm:p-6">
                <div class="mb-5 flex items-baseline justify-between gap-3"><h2 class="font-display text-lg font-bold">Cadastrar OSC</h2><span class="text-[11px] text-muted">Vinculada à sua conta</span></div>
                <form method="POST" action="{{ url('/admin/oscs') }}" class="space-y-3">
                    @csrf
                    <label class="block text-xs font-bold" for="nome">Nome da organização
                        <input id="nome" name="nome" value="{{ old('nome') }}" maxlength="150" required class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                        @error('nome') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                    </label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-xs font-bold" for="area_atuacao">Área de atuação
                            <input id="area_atuacao" name="area_atuacao" value="{{ old('area_atuacao') }}" maxlength="100" class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                            @error('area_atuacao') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                        </label>
                        <label class="block text-xs font-bold" for="telefone">Telefone
                            <input id="telefone" name="telefone" type="tel" value="{{ old('telefone') }}" maxlength="20" class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                            @error('telefone') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <label class="block text-xs font-bold" for="endereco">Endereço
                        <input id="endereco" name="endereco" value="{{ old('endereco') }}" maxlength="255" class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                        @error('endereco') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                    </label>
                    <label class="block text-xs font-bold" for="descricao">Descrição
                        <textarea id="descricao" name="descricao" rows="3" class="mt-1.5 w-full rounded-lg border border-line bg-[#fbfcfb] p-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">{{ old('descricao') }}</textarea>
                        @error('descricao') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                    </label>
                    <label class="block text-xs font-bold" for="email">E-mail de acesso da OSC
                        <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="150" autocomplete="email" required class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                        @error('email') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                    </label>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-xs font-bold" for="password">Senha inicial
                            <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                            @error('password') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
                        </label>
                        <label class="block text-xs font-bold" for="password_confirmation">Confirmar senha
                            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required class="mt-1.5 h-11 w-full rounded-lg border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                        </label>
                    </div>
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-primary px-4 text-sm font-bold text-white shadow-[0_9px_20px_rgba(18,107,98,0.17)] transition hover:bg-primary-dark">Cadastrar organização</button>
                </form>
            </section>

            <section class="min-w-0 border border-line bg-white p-5 sm:p-6">
                <div class="mb-4 flex items-baseline justify-between gap-3"><h2 class="font-display text-lg font-bold">Minhas OSCs</h2><span class="text-[11px] text-muted">{{ $oscs->count() }} no total</span></div>
                @if ($oscs->isEmpty())
                    <p class="border border-dashed border-[#bdcec9] px-4 py-8 text-center text-sm text-muted">Ainda não há OSCs cadastradas nesta conta.</p>
                @else
                    <div class="overflow-x-auto"><table class="w-full min-w-[500px] border-collapse text-left text-xs">
                        <thead><tr class="bg-[#f5f8f6] text-[10px] uppercase tracking-wide text-muted"><th class="px-3 py-3 font-bold">Organização</th><th class="px-3 py-3 font-bold">Área</th><th class="px-3 py-3 font-bold">Projetos</th></tr></thead>
                        <tbody>
                            @foreach ($oscs as $osc)
                                <tr class="border-b border-[#edf1ef] align-top">
                                    <td class="px-3 py-3"><strong>{{ $osc->nome }}</strong><small class="mt-1 block text-[10px] text-muted">{{ $osc->email }}</small></td>
                                    <td class="px-3 py-3">{{ $osc->area_atuacao ?: 'Não informada' }}</td>
                                    <td class="px-3 py-3">{{ $osc->demandas_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table></div>
                @endif

                <div class="mt-6 border-t border-line pt-5">
                    <div class="mb-4 flex items-baseline justify-between gap-3"><h3 class="font-display text-sm font-bold">Projetos e demandas</h3><span class="text-[11px] text-muted">{{ $demandas->count() }} no total</span></div>
                    @if ($demandas->isEmpty())
                        <p class="px-3 py-6 text-center text-xs text-muted">Os projetos das OSCs cadastradas aparecerão aqui.</p>
                    @else
                        <div class="overflow-x-auto"><table class="w-full min-w-[560px] border-collapse text-left text-xs">
                            <thead><tr class="bg-[#f5f8f6] text-[10px] uppercase tracking-wide text-muted"><th class="px-3 py-3 font-bold">Projeto</th><th class="px-3 py-3 font-bold">OSC</th><th class="px-3 py-3 font-bold">Status</th><th class="px-3 py-3 font-bold">Interesses</th></tr></thead>
                            <tbody>
                                @foreach ($demandas as $demanda)
                                    <tr class="border-b border-[#edf1ef] align-top">
                                        <td class="px-3 py-3"><strong>{{ $demanda->titulo }}</strong><small class="mt-1 block text-[10px] text-muted">{{ $demanda->data_criacao ? \Illuminate\Support\Carbon::parse($demanda->data_criacao)->format('d/m/Y') : 'Data não informada' }}</small></td>
                                        <td class="px-3 py-3">{{ $demanda->osc?->nome ?? 'OSC' }}</td>
                                        <td class="px-3 py-3"><span class="inline-flex rounded-full bg-primary-light px-2 py-1 text-[9px] font-extrabold uppercase text-primary">{{ $demanda->status }}</span></td>
                                        <td class="px-3 py-3">{{ $demanda->manifestacoes_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table></div>
                    @endif
                </div>
            </section>
        </div>
    </main>
</body>
</html>