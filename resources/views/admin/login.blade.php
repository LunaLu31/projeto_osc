<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Entrada administrativa | Redcam</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        colors: {
                            ink: '#18304b',
                            muted: '#5e7188',
                            primary: '#347db8',
                            'primary-dark': '#245d8d',
                            'primary-light': '#e2f1fc',
                            canvas: '#f5f9fd',
                            line: '#dce8f2',
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

<body class="grid min-h-screen place-items-center bg-[radial-gradient(circle_at_12%_18%,rgba(167,218,250,0.26),transparent_27%),linear-gradient(118deg,#eaf6ff_0%,#f8fbff_54%,#e3f1fc_100%)] px-5 py-8 font-sans text-ink">
    <main class="w-full max-w-[440px] rounded-2xl border border-line bg-white p-7 shadow-[0_22px_60px_rgba(28,66,103,0.12)] sm:p-10">
        <div class="mb-9 flex items-center gap-3">
            <span class="grid size-11 place-items-center rounded-[14px_14px_14px_5px] bg-primary font-display text-lg font-extrabold text-white">R</span>
            <span><strong class="block font-display text-sm tracking-[0.08em]">Redcam</strong><small class="mt-1 block text-xs text-muted">Portal administrativo</small></span>
        </div>
        <span class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-primary">Acesso restrito</span>
        <h1 class="mt-2 font-display text-3xl font-bold leading-tight">Entrar na gestão</h1>
        <p class="mb-6 mt-2 text-sm leading-6 text-muted">Acesse as organizações e os projetos vinculados à sua conta administrativa.</p>
        <form method="POST" action="{{ url('/admin/login') }}" class="space-y-4">
            @csrf
            <label class="block text-xs font-bold" for="email">E-mail
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="mt-2 h-12 w-full rounded-xl border border-line bg-[#f8fbfe] px-3 text-sm font-normal outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/15">
            </label>
            <label class="block text-xs font-bold" for="password">Senha
                <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-2 h-12 w-full rounded-xl border border-line bg-[#f8fbfe] px-3 text-sm font-normal outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/15">
            </label>
            @error('email') <p class="rounded-lg bg-[#fff0ec] px-3 py-2 text-xs font-semibold text-[#a33f2d]" role="alert">{{ $message }}</p> @enderror
            @error('password') <p class="rounded-lg bg-[#fff0ec] px-3 py-2 text-xs font-semibold text-[#a33f2d]" role="alert">{{ $message }}</p> @enderror
            <button type="submit" class="mt-2 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-primary px-5 text-sm font-bold text-white shadow-[0_9px_20px_rgba(18,107,98,0.17)] transition hover:-translate-y-0.5 hover:bg-primary-dark">Entrar no portal</button>
        </form>
        <a class="mt-6 inline-block text-xs text-muted transition hover:text-primary" href="{{ url('/') }}">Voltar para a página pública</a>
    </main>
</body>

</html>
