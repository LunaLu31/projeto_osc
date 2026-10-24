import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import Campo from '@/components/Campo';
import LayoutAutenticacao from '@/layouts/LayoutAutenticacao';

/** Primeiro acesso: cria o administrador inicial (só existe enquanto não houver nenhum). */
export default function Configuracao() {
    const { data, setData, post, errors, processing } = useForm({
        nome: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const criar = (evento: FormEvent) => {
        evento.preventDefault();
        post(route('admin.configuracao'));
    };

    return (
        <LayoutAutenticacao titulo="Configuração inicial" subtitulo="Configuração inicial">
            <span className="eyebrow">Primeiro acesso</span>
            <h1>Criar conta administrativa</h1>
            <p>Esta tela aparece somente enquanto ainda não houver um administrador cadastrado.</p>

            <form onSubmit={criar}>
                <Campo rotulo="Nome" erro={errors.nome}>
                    <input
                        value={data.nome}
                        onChange={(e) => setData('nome', e.target.value)}
                        autoComplete="name"
                        maxLength={100}
                        required
                    />
                </Campo>
                <Campo rotulo="E-mail" erro={errors.email}>
                    <input
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        autoComplete="email"
                        maxLength={150}
                        required
                    />
                </Campo>
                <Campo rotulo="Senha" erro={errors.password} dica="Mínimo de 8 caracteres, com letras e números.">
                    <input
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        autoComplete="new-password"
                        minLength={8}
                        required
                    />
                </Campo>
                <Campo rotulo="Confirmar senha">
                    <input
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        autoComplete="new-password"
                        minLength={8}
                        required
                    />
                </Campo>
                <button type="submit" className="button button--primary button--block mt-6" disabled={processing}>
                    Criar administrador e continuar
                </button>
            </form>
        </LayoutAutenticacao>
    );
}
