import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import AcoesFormulario from '@/components/AcoesFormulario';
import Campo from '@/components/Campo';
import LayoutOsc from '@/layouts/LayoutOsc';
import { CARTAO_FORMULARIO, GRADE_FORMULARIO } from '@/lib/classes';

/** Troca da senha inicial da OSC. */
export default function Senha() {
    const { data, setData, put, errors, processing, reset } = useForm({
        senha_atual: '',
        password: '',
        password_confirmation: '',
    });

    const salvar = (evento: FormEvent) => {
        evento.preventDefault();
        put(route('osc.senha.update'), { onFinish: () => reset() });
    };

    return (
        <LayoutOsc titulo="Alterar senha">
            <div className="page--narrow">
                <form onSubmit={salvar} className={CARTAO_FORMULARIO}>
                    <div className="form-card__header">
                        <span className="eyebrow">Segurança</span>
                        <h1>Alterar senha</h1>
                        <p>
                            Troque a senha inicial recebida da equipe CONECTA_OSC por uma que só a sua organização
                            conheça.
                        </p>
                    </div>

                    <Campo rotulo="Senha atual" erro={errors.senha_atual}>
                        <input
                            type="password"
                            value={data.senha_atual}
                            onChange={(e) => setData('senha_atual', e.target.value)}
                            autoComplete="current-password"
                            required
                        />
                    </Campo>
                    <div className={GRADE_FORMULARIO}>
                        <Campo
                            rotulo="Nova senha"
                            erro={errors.password}
                            dica="Mínimo de 8 caracteres, com letras e números."
                        >
                            <input
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                autoComplete="new-password"
                                minLength={8}
                                required
                            />
                        </Campo>
                        <Campo rotulo="Confirmar nova senha">
                            <input
                                type="password"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                autoComplete="new-password"
                                minLength={8}
                                required
                            />
                        </Campo>
                    </div>

                    <AcoesFormulario>
                        <Link href={route('osc.painel')} className="button button--secondary">
                            Cancelar
                        </Link>
                        <button type="submit" className="button button--primary" disabled={processing}>
                            Salvar nova senha
                        </button>
                    </AcoesFormulario>
                </form>
            </div>
        </LayoutOsc>
    );
}
