import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import CartaoPainel from '@/components/CartaoPainel';
import GraficoBarras from '@/components/graficos/GraficoBarras';
import GraficoColunas from '@/components/graficos/GraficoColunas';
import StatusBadge from '@/components/StatusBadge';
import TituloPainel from '@/components/TituloPainel';
import LayoutAdmin from '@/layouts/LayoutAdmin';
import { formatarData, formatarDiaDoInstante, plural } from '@/lib/formatar';
import { formatarMes } from '@/lib/graficos';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda, ListaResumo, MetricasAdmin, Osc, StatusDemanda } from '@/types';

interface PainelProps {
    metricas: MetricasAdmin;
    prazosVencidos: ListaResumo<Demanda>;
    oscsSemDemandasAtivas: ListaResumo<Osc>;
}

const demandas = (total: number) => plural(total, 'demanda', 'demandas');
const manifestacoes = (total: number) => plural(total, 'manifestação', 'manifestações');
const oscs = (total: number) => plural(total, 'OSC', 'OSCs');

/** Visão geral do administrador: gráficos da rede e listas que pedem ação (RF09). */
export default function Painel({ metricas, prazosVencidos, oscsSemDemandasAtivas }: PainelProps) {
    const { auth, referencias } = usePropsPagina();

    const totalDemandas = Object.values(metricas.demandas_por_status).reduce((soma, total) => soma + total, 0);
    const manifestacoesPorMes = metricas.manifestacoes_por_mes.map(({ mes, total }) => ({
        chave: mes,
        rotulo: formatarMes(mes),
        rotuloCompleto: formatarMes(mes, true),
        valor: total,
    }));
    // Já vem da maior para a menor; categorias sem demanda ativa ficam de fora.
    const categorias = Object.entries(metricas.demandas_ativas_por_categoria)
        .filter(([, total]) => total > 0)
        .map(([categoria, total]) => ({ chave: categoria, rotulo: categoria, valor: total }));

    return (
        <LayoutAdmin titulo="Painel administrativo">
            <TituloPainel
                rotulo="Visão geral"
                titulo={`Olá, ${auth.admin?.nome}`}
                descricao="Acompanhe a saúde da rede e o que precisa da sua atenção."
            />

            <div className="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2">
                <CartaoPainel className="md:col-span-2">
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Engajamento</span>
                            <h2>Manifestações por mês</h2>
                        </div>
                        <span>últimos {manifestacoesPorMes.length} meses</span>
                    </div>
                    {manifestacoesPorMes.some((ponto) => ponto.valor > 0) ? (
                        <GraficoColunas
                            titulo={`Manifestações de interesse na rede por mês, últimos ${manifestacoesPorMes.length} meses`}
                            dados={manifestacoesPorMes}
                            colunas={['Mês', 'Manifestações']}
                            formatarValor={manifestacoes}
                        />
                    ) : (
                        <p className="empty-state">
                            Nenhuma manifestação nos últimos {manifestacoesPorMes.length} meses. O gráfico aparece com o
                            primeiro interesse recebido na rede.
                        </p>
                    )}
                </CartaoPainel>

                <CartaoPainel>
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Andamento</span>
                            <h2>Demandas por status</h2>
                        </div>
                        <span>{demandas(totalDemandas)}</span>
                    </div>
                    {totalDemandas > 0 ? (
                        <GraficoBarras
                            titulo="Demandas da rede por status"
                            itens={(Object.keys(referencias.status) as StatusDemanda[]).map((status) => ({
                                chave: status,
                                rotulo: referencias.status[status],
                                valor: metricas.demandas_por_status[status] ?? 0,
                            }))}
                            formatarValor={demandas}
                        />
                    ) : (
                        <p className="empty-state">As demandas publicadas pelas OSCs aparecerão aqui.</p>
                    )}
                </CartaoPainel>

                <CartaoPainel>
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Necessidades</span>
                            <h2>Demandas ativas por categoria</h2>
                        </div>
                        <span>{demandas(metricas.demandas_ativas)}</span>
                    </div>
                    {categorias.length > 0 ? (
                        <GraficoBarras
                            titulo="Demandas ativas por categoria"
                            itens={categorias}
                            formatarValor={demandas}
                        />
                    ) : (
                        <p className="empty-state">Nenhuma demanda ativa na rede no momento.</p>
                    )}
                </CartaoPainel>

                <CartaoPainel>
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Atenção</span>
                            <h2>Prazos vencidos</h2>
                        </div>
                        {prazosVencidos.total > 0 ? (
                            <Link href={route('admin.demandas.index')} className="text-link">
                                Moderar {demandas(prazosVencidos.total)}
                            </Link>
                        ) : (
                            <span>em dia</span>
                        )}
                    </div>
                    {prazosVencidos.itens.length === 0 ? (
                        <p className="empty-state">Nenhuma demanda ativa está com o prazo vencido.</p>
                    ) : (
                        <ul className="lista-resumo">
                            {prazosVencidos.itens.map((demanda) => (
                                <li key={demanda.id}>
                                    <div>
                                        <strong>{demanda.titulo}</strong>
                                        <small>
                                            {demanda.osc?.nome}
                                            <StatusBadge status={demanda.status} />
                                            {demanda.urgente && <span className="badge badge--urgent">Urgente</span>}
                                        </small>
                                    </div>
                                    <time dateTime={demanda.prazo ?? undefined}>
                                        venceu em {formatarData(demanda.prazo)}
                                    </time>
                                </li>
                            ))}
                        </ul>
                    )}
                </CartaoPainel>

                <CartaoPainel>
                    <div className="section-heading--compact">
                        <div>
                            <span className="eyebrow">Acompanhamento</span>
                            <h2>OSCs sem demandas ativas</h2>
                        </div>
                        <span>{oscs(oscsSemDemandasAtivas.total)}</span>
                    </div>
                    {oscsSemDemandasAtivas.itens.length === 0 ? (
                        <p className="empty-state">Todas as OSCs têm ao menos uma demanda ativa.</p>
                    ) : (
                        <ul className="lista-resumo">
                            {oscsSemDemandasAtivas.itens.map((osc) => (
                                <li key={osc.id}>
                                    <div>
                                        <strong>{osc.nome}</strong>
                                        <small>
                                            {osc.total_demandas
                                                ? plural(osc.total_demandas, 'demanda concluída', 'demandas concluídas')
                                                : 'Nenhuma demanda publicada'}
                                            {osc.cadastrada_em &&
                                                ` · desde ${formatarDiaDoInstante(osc.cadastrada_em)}`}
                                        </small>
                                    </div>
                                    <Link
                                        href={route('admin.oscs.edit', osc.id)}
                                        className="text-link"
                                        aria-label={`Ver dados de ${osc.nome}`}
                                    >
                                        Ver dados
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    )}
                </CartaoPainel>
            </div>
        </LayoutAdmin>
    );
}
