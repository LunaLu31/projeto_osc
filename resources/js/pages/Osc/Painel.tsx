import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import CartaoPainel from '@/components/CartaoPainel';
import Icone from '@/components/Icone';
import MetricaCard from '@/components/MetricaCard';
import StatusBadge from '@/components/StatusBadge';
import TituloPainel from '@/components/TituloPainel';
import LayoutOsc from '@/layouts/LayoutOsc';
import { formatarData, formatarDataHora } from '@/lib/formatar';
import { usePropsPagina } from '@/lib/pagina';
import type { Contato, Demanda, MetricasOsc } from '@/types';

interface PainelProps {
    metricas: MetricasOsc;
    diasRecentes: number;
    contatosRecentes: Contato[];
    prazosProximos: Demanda[];
    diasPrazoProximo: number;
}

/** Visão geral do painel da OSC: indicadores, contatos recentes e prazos próximos. */
export default function Painel({
    metricas,
    diasRecentes,
    contatosRecentes,
    prazosProximos,
    diasPrazoProximo,
}: PainelProps) {
    const { auth } = usePropsPagina();

    return (
        <LayoutOsc titulo="Painel da OSC">
            <TituloPainel
                rotulo="Visão geral"
                titulo={`Olá, ${auth.osc?.nome}`}
                descricao="Acompanhe suas demandas e conexões em um só lugar."
                acao={
                    <Link href={route('osc.demandas.create')} className="button button--primary">
                        <Icone nome="plus" /> Nova demanda
                    </Link>
                }
            />

            <section className="metric-grid my-8 grid grid-cols-1 gap-4 md:grid-cols-3" aria-label="Indicadores">
                <MetricaCard
                    icone="box"
                    cor="blue"
                    valor={metricas.demandas_ativas}
                    rotulo="Demandas ativas"
                    detalhe={`${metricas.demandas_por_status.Parcial} parcialmente atendidas`}
                />
                <MetricaCard
                    icone="people"
                    cor="green"
                    valor={metricas.total_contatos}
                    rotulo="Contatos recebidos"
                    detalhe={`${metricas.contatos_recentes} nos últimos ${diasRecentes} dias`}
                />
                <MetricaCard
                    icone="check"
                    cor="orange"
                    valor={metricas.demandas_concluidas}
                    rotulo="Demandas concluídas"
                />
            </section>

            <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                <CartaoPainel>
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Quem quer ajudar</span>
                            <h2>Contatos recentes</h2>
                        </div>
                        <Link href={route('osc.contatos.index')} className="text-link">
                            Ver todos
                        </Link>
                    </div>
                    {contatosRecentes.length === 0 ? (
                        <p className="empty-state">
                            Os contatos de quem manifestar interesse nas suas demandas aparecerão aqui.
                        </p>
                    ) : (
                        <ul className="lista-resumo">
                            {contatosRecentes.map((contato) => (
                                <li key={contato.id}>
                                    <div>
                                        <strong>{contato.nome}</strong>
                                        <small>
                                            {contato.tipo_interesse} · {contato.demanda?.titulo}
                                        </small>
                                    </div>
                                    <time dateTime={contato.recebido_em ?? undefined}>
                                        {formatarDataHora(contato.recebido_em, true)}
                                    </time>
                                </li>
                            ))}
                        </ul>
                    )}
                </CartaoPainel>

                <CartaoPainel>
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Atenção</span>
                            <h2>Prazos próximos</h2>
                        </div>
                        <span>próximos {diasPrazoProximo} dias</span>
                    </div>
                    {prazosProximos.length === 0 ? (
                        <p className="empty-state">Nenhuma demanda ativa vence nos próximos {diasPrazoProximo} dias.</p>
                    ) : (
                        <ul className="lista-resumo">
                            {prazosProximos.map((demanda) => (
                                <li key={demanda.id}>
                                    <div>
                                        <strong>{demanda.titulo}</strong>
                                        <small>
                                            <StatusBadge status={demanda.status} />
                                            {demanda.urgente && <span className="badge badge--urgent">Urgente</span>}
                                        </small>
                                    </div>
                                    <Link href={route('osc.demandas.edit', demanda.id)} className="text-link">
                                        {formatarData(demanda.prazo)}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    )}
                </CartaoPainel>
            </div>
        </LayoutOsc>
    );
}
