import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import Campo from '@/components/Campo';
import Icone from '@/components/Icone';
import ManterConectado from '@/components/ManterConectado';
import LayoutAutenticacao from '@/layouts/LayoutAutenticacao';

/** Login da OSC (RF01). */
export default function Login() {
    const { data, setData, post, errors, processing, reset } = useForm({ email: '', password: '', lembrar: false });

    const entrar = (evento: FormEvent) => {
        evento.preventDefault();
        post(route('osc.login'), { onFinish: () => reset('password') });
    };

    return (
        <LayoutAutenticacao titulo="Área da OSC" subtitulo="Área da OSC">
            <span className="eyebrow">Área restrita</span>
            <h1>Acesse sua organização</h1>
            <p>Gerencie suas demandas e acompanhe quem quer ajudar.</p>

            <form onSubmit={entrar}>
                <Campo rotulo="E-mail" erro={errors.email}>
                    <input
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        autoComplete="username"
                        required
                        autoFocus
                    />
                </Campo>
                <Campo rotulo="Senha" erro={errors.password}>
                    <input
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        autoComplete="current-password"
                        required
                    />
                </Campo>
                <ManterConectado marcado={data.lembrar} alterar={(marcado) => setData('lembrar', marcado)} />
                <button type="submit" className="button button--primary button--block mt-6" disabled={processing}>
                    {processing ? 'Entrando…' : 'Entrar no painel'} <Icone nome="arrow" />
                </button>
            </form>

            <p className="auth-card__footer field__hint">
                Esqueceu a senha? Fale com a equipe UOSCC para gerar uma nova.
            </p>
            <div className="auth-card__footer">
                <Link href={route('feed')} className="back-link mb-0">
                    <Icone nome="back" tamanho={16} /> Voltar para a página pública
                </Link>
            </div>
        </LayoutAutenticacao>
    );
}
