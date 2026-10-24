import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import BotaoExcluir from '@/components/BotaoExcluir';
import CartaoPainel from '@/components/CartaoPainel';
import { EsqueletoLinhas } from '@/components/Esqueleto';
import Icone from '@/components/Icone';
import SeletorStatus from '@/components/SeletorStatus';
import TituloPainel from '@/components/TituloPainel';
import { useRecarregandoPagina } from '@/hooks/useRecarregandoPagina';
import LayoutOsc from '@/layouts/LayoutOsc';
import { formatarData, plural } from '@/lib/formatar';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda, StatusDemanda } from '@/types';

interface DemandasProps {
    demandas: Demanda[];
    statusAtual: StatusDemanda | null;
    totaisPorStatus: Partial<Record<StatusDemanda, number>>;
}

/** Gestão das demandas da OSC (RF03) com alteração de status (RF07). */
export default function Demandas({ demandas, statusAtual, totaisPorStatus }: DemandasProps) {
    const { referencias } = usePropsPagina();
    const carregando = useRecarregandoPagina();
    const total = Object.values(totaisPorStatus).reduce((soma, valor) => soma + Number(valor ?? 0), 0);

    return (
        <LayoutOsc titulo="Minhas demandas">
            <TituloPainel
                rotulo="Gestão"
                titulo="Minhas demandas"
                descricao="Publique, edite e atualize o status das necessidades da sua organização."
                acao={
                    <Link href={route('osc.demandas.create')} className="button button--primary">
                        <Icone nome="plus" /> Nova demanda
                    </Link>
                }
            />

            <CartaoPainel className="mt-8">
                <nav className="filtro-abas" aria-label="Filtrar por status">
                    <Link
                        href={route('osc.demandas.index')}
                        aria-current={!statusAtual ? 'page' : undefined}
                        preserveScroll
                    >
                        Todas <span>{total}</span>
                    </Link>
                    {(Object.keys(referencias.status) as StatusDemanda[]).map((status) => (
                        <Link
                            key={status}
                            href={route('osc.demandas.index', { status })}
                            aria-current={statusAtual === status ? 'page' : undefined}
                            preserveScroll
                        >
                            {referencias.status[status]} <span>{totaisPorStatus[status] ?? 0}</span>
                        </Link>
                    ))}
                </nav>

                {demandas.length === 0 ? (
                    <div className="empty-state">
                        <Icone nome="box" tamanho={36} />
                        {statusAtual ? (
                            <>
                                <h3>Nenhuma demanda com este status</h3>
                                <p>Escolha outro filtro para ver as demais demandas.</p>
                            </>
                        ) : (
                            <>
                                <h3>Nenhuma demanda publicada</h3>
                                <p>
                                    Publique a primeira necessidade da sua organização para que apoiadores possam
                                    ajudar.
                                </p>
                                <Link href={route('osc.demandas.create')} className="button button--primary">
                                    <Icone nome="plus" /> Nova demanda
                                </Link>
                            </>
                        )}
                    </div>
                ) : (
                    <div className="table-wrap">
                        <table className="data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Demanda</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Prazo</th>
                                    <th scope="col">Contatos</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody aria-busy={carregando}>
                                {carregando ? (
                                    <EsqueletoLinhas colunas={5} linhas={demandas.length} />
                                ) : (
                                    demandas.map((demanda) => (
                                        <tr key={demanda.id}>
                                            <td>
                                                <div className="data-table__title">
                                                    <strong>{demanda.titulo}</strong>
                                                    <small>
                                                        {demanda.categoria} · {demanda.quantidade} un.
                                                        {demanda.urgente && (
                                                            <>
                                                                {' · '}
                                                                <span className="font-bold text-coral">Urgente</span>
                                                            </>
                                                        )}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <SeletorStatus
                                                    demanda={demanda}
                                                    url={route('osc.demandas.status', demanda.id)}
                                                />
                                            </td>
                                            <td>{formatarData(demanda.prazo) ?? 'Sem prazo'}</td>
                                            <td>
                                                {demanda.total_contatos ? (
                                                    <Link
                                                        href={route('osc.contatos.index', { demanda: demanda.id })}
                                                        className="text-link"
                                                    >
                                                        {plural(demanda.total_contatos, 'contato', 'contatos')}
                                                    </Link>
                                                ) : (
                                                    <span className="text-muted">Nenhum ainda</span>
                                                )}
                                            </td>
                                            <td>
                                                <div className="table-actions">
                                                    {demanda.status !== 'Concluída' && (
                                                        <a
                                                            href={route('demandas.show', demanda.id)}
                                                            className="button button--secondary button--small"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                        >
                                                            Ver
                                                        </a>
                                                    )}
                                                    <Link
                                                        href={route('osc.demandas.edit', demanda.id)}
                                                        className="button button--secondary button--small"
                                                    >
                                                        <Icone nome="edit" tamanho={15} /> Editar
                                                    </Link>
                                                    <BotaoExcluir
                                                        url={route('osc.demandas.destroy', demanda.id)}
                                                        titulo={`Excluir a demanda “${demanda.titulo}”?`}
                                                        descricao="As manifestações de interesse recebidas também serão apagadas. Esta ação não pode ser desfeita."
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
            </CartaoPainel>
        </LayoutOsc>
    );
}
