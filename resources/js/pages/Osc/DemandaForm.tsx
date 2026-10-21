import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import AcoesFormulario from '@/components/AcoesFormulario';
import Campo from '@/components/Campo';
import Icone from '@/components/Icone';
import LayoutOsc from '@/layouts/LayoutOsc';
import { hojeIso } from '@/lib/formatar';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda } from '@/types';
import { CARTAO_FORMULARIO, GRADE_FORMULARIO } from '@/lib/classes';

/** Criação e edição de demanda pela OSC (RF03). */
export default function DemandaForm({ demanda }: { demanda: Demanda | null }) {
    const { referencias } = usePropsPagina();
    const editando = demanda !== null;

    const { data, setData, post, put, errors, processing } = useForm({
        titulo: demanda?.titulo ?? '',
        categoria: demanda?.categoria ?? '',
        quantidade: demanda?.quantidade ? String(demanda.quantidade) : '',
        descricao: demanda?.descricao ?? '',
        prazo: demanda?.prazo ?? '',
        urgencia: demanda?.urgente ?? false,
    });

    const salvar = (evento: FormEvent) => {
        evento.preventDefault();

        if (editando) {
            put(route('osc.demandas.update', demanda.id));
        } else {
            post(route('osc.demandas.store'));
        }
    };

    return (
        <LayoutOsc titulo={editando ? 'Editar demanda' : 'Nova demanda'}>
            <div className="page--narrow">
                <Link href={route('osc.demandas.index')} className="back-link">
                    <Icone nome="back" tamanho={16} /> Voltar para as demandas
                </Link>

                <form onSubmit={salvar} className={CARTAO_FORMULARIO}>
                    <div className="form-card__header">
                        <span className="eyebrow">{editando ? 'Edição' : 'Nova publicação'}</span>
                        <h1>{editando ? 'Editar demanda' : 'Criar demanda'}</h1>
                        <p>Descreva de forma clara o que sua organização precisa.</p>
                    </div>

                    <Campo rotulo="Título" erro={errors.titulo}>
                        <input
                            type="text"
                            value={data.titulo}
                            onChange={(e) => setData('titulo', e.target.value)}
                            placeholder="Ex.: 30 kits de material escolar"
                            maxLength={150}
                            required
                        />
                    </Campo>
                    <div className={GRADE_FORMULARIO}>
                        <Campo rotulo="Categoria" erro={errors.categoria}>
                            <select
                                value={data.categoria}
                                onChange={(e) => setData('categoria', e.target.value)}
                                required
                            >
                                <option value="">Selecione</option>
                                {referencias.categorias.map((categoria) => (
                                    <option key={categoria} value={categoria}>
                                        {categoria}
                                    </option>
                                ))}
                            </select>
                        </Campo>
                        <Campo rotulo="Quantidade" erro={errors.quantidade}>
                            <input
                                type="number"
                                min={1}
                                max={1_000_000}
                                value={data.quantidade}
                                onChange={(e) => setData('quantidade', e.target.value)}
                                placeholder="30"
                                required
                            />
                        </Campo>
                    </div>
                    <Campo rotulo="Descrição" erro={errors.descricao}>
                        <textarea
                            rows={5}
                            maxLength={5000}
                            value={data.descricao}
                            onChange={(e) => setData('descricao', e.target.value)}
                            placeholder="Explique a necessidade, para quem é e como entregar."
                            required
                        />
                    </Campo>
                    <Campo rotulo="Prazo" erro={errors.prazo}>
                        <input
                            type="date"
                            value={data.prazo}
                            min={editando ? undefined : hojeIso()}
                            onChange={(e) => setData('prazo', e.target.value)}
                            required
                        />
                    </Campo>
                    <label className="checkbox-field">
                        <input
                            type="checkbox"
                            checked={data.urgencia}
                            onChange={(e) => setData('urgencia', e.target.checked)}
                        />
                        <span>
                            <strong>Demanda urgente</strong>
                            <br />
                            Aparece em destaque no topo do feed.
                        </span>
                    </label>

                    <AcoesFormulario>
                        <Link href={route('osc.demandas.index')} className="button button--secondary">
                            Cancelar
                        </Link>
                        <button type="submit" className="button button--primary" disabled={processing}>
                            {editando ? 'Salvar alterações' : 'Publicar demanda'}
                        </button>
                    </AcoesFormulario>
                </form>
            </div>
        </LayoutOsc>
    );
}
