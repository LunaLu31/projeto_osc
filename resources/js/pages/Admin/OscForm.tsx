import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import AcoesFormulario from '@/components/AcoesFormulario';
import CamposOsc, { type DadosOsc } from '@/components/CamposOsc';
import Icone from '@/components/Icone';
import LayoutAdmin from '@/layouts/LayoutAdmin';
import type { Osc } from '@/types';
import { CARTAO_FORMULARIO } from '@/lib/classes';

/** Edição de uma OSC pelo administrador (RF09). */
export default function OscForm({ osc }: { osc: Osc }) {
    const formulario = useForm<DadosOsc>({
        nome: osc.nome,
        area_atuacao: osc.area_atuacao,
        telefone: osc.telefone ?? '',
        endereco: osc.endereco ?? '',
        descricao: osc.descricao ?? '',
        email: osc.email,
        password: '',
        password_confirmation: '',
    });

    const salvar = (evento: FormEvent) => {
        evento.preventDefault();
        formulario.put(route('admin.oscs.update', osc.id));
    };

    return (
        <LayoutAdmin titulo={`Editar ${osc.nome}`}>
            <div className="page--narrow">
                <Link href={route('admin.painel')} className="back-link">
                    <Icone nome="back" tamanho={16} /> Voltar para o painel
                </Link>

                <form onSubmit={salvar} className={CARTAO_FORMULARIO}>
                    <div className="form-card__header">
                        <span className="eyebrow">Gestão da rede</span>
                        <h1>Editar organização</h1>
                        <p>
                            Atualize os dados públicos e o acesso de <strong>{osc.nome}</strong>.
                        </p>
                    </div>

                    <CamposOsc formulario={formulario} editando />

                    <AcoesFormulario>
                        <Link href={route('admin.painel')} className="button button--secondary">
                            Cancelar
                        </Link>
                        <button type="submit" className="button button--primary" disabled={formulario.processing}>
                            Salvar alterações
                        </button>
                    </AcoesFormulario>
                </form>
            </div>
        </LayoutAdmin>
    );
}
