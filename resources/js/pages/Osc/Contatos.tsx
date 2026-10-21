import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import CartaoPainel from '@/components/CartaoPainel';
import { EsqueletoLinhas } from '@/components/Esqueleto';
import Campo from '@/components/Campo';
import Icone from '@/components/Icone';
import Paginacao from '@/components/Paginacao';
import TituloPainel from '@/components/TituloPainel';
import { useRecarregandoPagina } from '@/hooks/useRecarregandoPagina';
import LayoutOsc from '@/layouts/LayoutOsc';
import { apenasTelefone, formatarDataHora } from '@/lib/formatar';
import { usePropsPagina } from '@/lib/pagina';
import type { Contato, Paginado, TipoInteresse } from '@/types';

interface ContatosProps {
    contatos: Paginado<Contato>;
    demandas: { id: number; titulo: string }[];
    filtros: { demanda: number | null; tipo: TipoInteresse | null };
}

/** Contatos recebidos pela OSC (RF08). Visíveis só para a OSC responsável (RNF06). */
export default function Contatos({ contatos, demandas, filtros }: ContatosProps) {
    const { referencias } = usePropsPagina();
    const carregando = useRecarregandoPagina();

    const filtrar = (alteracao: Partial<ContatosProps['filtros']>) => {
        const consulta = Object.fromEntries(Object.entries({ ...filtros, ...alteracao }).filter(([, valor]) => valor));
        router.get(route('osc.contatos.index'), consulta, { preserveState: true, preserveScroll: true, replace: true });
    };

    return (
        <LayoutOsc titulo="Contatos recebidos">
            <TituloPainel
                rotulo="Quem quer ajudar"
                titulo="Contatos recebidos"
                descricao="Pessoas que manifestaram interesse nas demandas da sua organização."
            />

            <div className="privacy-note">
                <Icone nome="shield" />
                Use estes dados apenas para responder sobre a demanda indicada. Eles foram compartilhados com o
                consentimento do apoiador (LGPD).
            </div>

            <CartaoPainel>
                <div
                    className="filtros-painel mb-[22px] grid grid-cols-1 items-end gap-3 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]"
                    role="search"
                    aria-label="Filtrar contatos"
                >
                    <Campo rotulo="Demanda">
                        <select
                            value={filtros.demanda ?? ''}
                            onChange={(e) => filtrar({ demanda: e.target.value ? Number(e.target.value) : null })}
                        >
                            <option value="">Todas as demandas</option>
                            {demandas.map((demanda) => (
                                <option key={demanda.id} value={demanda.id}>
                                    {demanda.titulo}
                                </option>
                            ))}
                        </select>
                    </Campo>
                    <Campo rotulo="Tipo de contribuição">
                        <select
                            value={filtros.tipo ?? ''}
                            onChange={(e) => filtrar({ tipo: (e.target.value || null) as TipoInteresse | null })}
                        >
                            <option value="">Todos os tipos</option>
                            {referencias.tiposInteresse.map((tipo) => (
                                <option key={tipo} value={tipo}>
                                    {tipo}
                                </option>
                            ))}
                        </select>
                    </Campo>
                </div>

                {contatos.data.length === 0 ? (
                    <div className="empty-state">
                        <Icone nome="people" tamanho={36} />
                        <h3>Nenhum contato encontrado</h3>
                        <p>
                            {filtros.demanda || filtros.tipo
                                ? 'Tente outros filtros.'
                                : 'Quando alguém manifestar interesse nas suas demandas, o contato aparecerá aqui.'}
                        </p>
                    </div>
                ) : (
                    <>
                        <div className="table-wrap">
                            <table className="data-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Apoiador</th>
                                        <th scope="col">Contato</th>
                                        <th scope="col">Demanda</th>
                                        <th scope="col">Mensagem</th>
                                        <th scope="col">Recebido em</th>
                                    </tr>
                                </thead>
                                <tbody aria-busy={carregando}>
                                    {carregando ? (
                                        <EsqueletoLinhas colunas={5} linhas={contatos.data.length} />
                                    ) : (
                                        contatos.data.map((contato) => (
                                            <tr key={contato.id}>
                                                <td>
                                                    <div className="data-table__title">
                                                        <strong>{contato.nome}</strong>
                                                        <small>{contato.tipo_interesse}</small>
                                                    </div>
                                                </td>
                                                <td className="contato-meios">
                                                    {contato.telefone && (
                                                        <a href={`tel:${apenasTelefone(contato.telefone)}`}>
                                                            <Icone nome="phone" tamanho={15} /> {contato.telefone}
                                                        </a>
                                                    )}
                                                    {contato.email && (
                                                        <a href={`mailto:${contato.email}`}>
                                                            <Icone nome="mail" tamanho={15} /> {contato.email}
                                                        </a>
                                                    )}
                                                </td>
                                                <td>{contato.demanda?.titulo}</td>
                                                <td className="contato-mensagem">{contato.mensagem || '—'}</td>
                                                <td>{formatarDataHora(contato.recebido_em)}</td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                        <Paginacao pagina={contatos} rotulo="Paginação dos contatos" />
                    </>
                )}
            </CartaoPainel>
        </LayoutOsc>
    );
}
