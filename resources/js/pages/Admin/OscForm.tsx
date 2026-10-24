import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import AcoesFormulario from '@/components/AcoesFormulario';
import CamposOsc, { type DadosOsc } from '@/components/CamposOsc';
import Icone from '@/components/Icone';
import LayoutAdmin from '@/layouts/LayoutAdmin';
import type { Osc } from '@/types';
import { CARTAO_FORMULARIO } from '@/lib/classes';

/** Aba "Cadastro" (RF02) e edição de uma OSC pelo administrador (RF09). */
export default function OscForm({ osc }: { osc: Osc | null }) {
    const editando = osc !== null;

    const formulario = useForm<DadosOsc>({
        nome: osc?.nome ?? '',
        area_atuacao: osc?.area_atuacao ?? '',
        telefone: osc?.telefone ?? '',
        endereco: osc?.endereco ?? '',
        descricao: osc?.descricao ?? '',
        email: osc?.email ?? '',
        password: '',
        password_confirmation: '',
    });

    const salvar = (evento: FormEvent) => {
        evento.preventDefault();

        if (editando) {
            formulario.put(route('admin.oscs.update', osc.id));
        } else {
            formulario.post(route('admin.oscs.store'));
        }
    };

    return (
        <LayoutAdmin titulo={editando ? `Editar ${osc.nome}` : 'Cadastrar OSC'}>
            <div className="page--narrow">
                {editando && (
                    <Link href={route('admin.oscs.index')} className="back-link">
                        <Icone nome="back" tamanho={16} /> Voltar para a rede
                    </Link>
                )}

                <form onSubmit={salvar} className={CARTAO_FORMULARIO}>
                    <div className="form-card__header">
                        <span className="eyebrow">{editando ? 'Gestão da rede' : 'Cadastro'}</span>
                        <h1>{editando ? 'Editar organização' : 'Cadastrar organização'}</h1>
                        {editando ? (
                            <p>
                                Atualize os dados públicos e o acesso de <strong>{osc.nome}</strong>.
                            </p>
                        ) : (
                            <p>Registre uma nova OSC na rede e defina o acesso inicial ao painel.</p>
                        )}
                    </div>

                    <CamposOsc formulario={formulario} editando={editando} />

                    <AcoesFormulario>
                        <Link href={route('admin.oscs.index')} className="button button--secondary">
                            Cancelar
                        </Link>
                        <button type="submit" className="button button--primary" disabled={formulario.processing}>
                            {editando ? (
                                'Salvar alterações'
                            ) : (
                                <>
                                    <Icone nome="plus" /> Cadastrar organização
                                </>
                            )}
                        </button>
                    </AcoesFormulario>
                </form>
            </div>
        </LayoutAdmin>
    );
}
