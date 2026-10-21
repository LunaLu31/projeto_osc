import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import Campo from '@/components/Campo';
import Icone from '@/components/Icone';
import LayoutAutenticacao from '@/layouts/LayoutAutenticacao';

/** Login do administrador (RF01). */
export default function Login() {
    const { data, setData, post, errors, processing, reset } = useForm({ email: '', password: '' });

    const entrar = (evento: FormEvent) => {
        evento.preventDefault();
        post(route('admin.login'), { onFinish: () => reset('password') });
    };

    return (
        <LayoutAutenticacao titulo="Portal administrativo" subtitulo="Portal administrativo">
            <span className="eyebrow">Acesso restrito</span>
            <h1>Entrar na gestão</h1>
            <p>Gerencie as OSCs da rede e acompanhe as demandas publicadas.</p>

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
                <button type="submit" className="button button--primary button--block mt-6" disabled={processing}>
                    {processing ? 'Entrando…' : 'Entrar no portal'} <Icone nome="arrow" />
                </button>
            </form>
        </LayoutAutenticacao>
    );
}
