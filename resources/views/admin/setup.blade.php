<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configuração inicial | Redcam</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        colors: {
                            ink: '#15312f',
                            muted: '#617572',
                            primary: '#126b62',
                            'primary-dark': '#0a4f49',
                            'primary-light': '#dff3ed',
                            canvas: '#f6f8f5',
                            line: '#dfe8e4',
                            coral: '#e55f46'
                        },
                        fontFamily: {
                            sans: ['DM Sans', 'sans-serif'],
                            display: ['Manrope', 'sans-serif']
                        }
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="grid min-h-screen place-items-center bg-[radial-gradient(circle_at_12%_18%,rgba(217,239,139,0.22),transparent_24%),linear-gradient(118deg,#eff8f2_0%,#f7faf6_55%,#e4f2ec_100%)] px-5 py-8 font-sans text-ink">
    <main class="w-full max-w-[500px] rounded-2xl border border-line bg-white p-7 shadow-[0_22px_60px_rgba(21,49,47,0.12)] sm:p-10">
        <div class="mb-8 flex items-center gap-3">
            <span class="grid size-11 place-items-center rounded-[14px_14px_14px_5px] bg-primary font-display text-lg font-extrabold text-white">R</span>
            <span><strong class="block font-display text-sm tracking-[0.08em]">Redcam</strong><small class="mt-1 block text-xs text-muted">Configuração inicial</small></span>
        </div>
        <span class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-primary">Primeiro acesso</span>
        <h1 class="mt-2 font-display text-3xl font-bold leading-tight">Criar conta administrativa</h1>
        <p class="mb-5 mt-2 text-sm leading-6 text-muted">Esta configuração aparece somente enquanto ainda não houver um administrador cadastrado.</p>
        <form method="POST" action="{{ url('/admin/setup') }}" class="space-y-3">
            @csrf
            <label class="block text-xs font-bold" for="nome">Nome
                <input id="nome" name="nome" value="{{ old('nome') }}" autocomplete="name" maxlength="100" required class="mt-2 h-11 w-full rounded-xl border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                @error('nome') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
            </label>
            <label class="block text-xs font-bold" for="email">E-mail
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="150" required class="mt-2 h-11 w-full rounded-xl border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                @error('email') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
            </label>
            <label class="block text-xs font-bold" for="password">Senha (mínimo de 8 caracteres)
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required class="mt-2 h-11 w-full rounded-xl border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                @error('password') <span class="mt-1 block text-[11px] font-medium text-coral">{{ $message }}</span> @enderror
            </label>
            <label class="block text-xs font-bold" for="password_confirmation">Confirmar senha
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required class="mt-2 h-11 w-full rounded-xl border border-line bg-[#fbfcfb] px-3 text-sm font-normal outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
            </label>
            <button type="submit" class="mt-3 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary-dark">Criar administrador e continuar</button>
        </form>
    </main>
</body>

</html>
