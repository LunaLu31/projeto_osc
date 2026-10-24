import { Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { route } from 'ziggy-js';
import { EsqueletoCartoes } from '@/components/Esqueleto';
import Icone from '@/components/Icone';
import Paginacao from '@/components/Paginacao';
import StatusBadge from '@/components/StatusBadge';
import { useRecarregandoPagina } from '@/hooks/useRecarregandoPagina';
import LayoutSite from '@/layouts/LayoutSite';
import { formatarData, plural } from '@/lib/formatar';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda, MetricasRede, Paginado } from '@/types';

interface Filtros {
    busca: string;
    categoria: string | null;
    status: string | null;
    urgente: boolean;
}

interface FeedProps {
    demandas: Paginado<Demanda>;
    filtros: Filtros;
    metricas: MetricasRede;
    iniciaisOscsDestaque: string[];
}

/**
 * Página inicial: apresentação, métricas e listagem pública de demandas (RF04).
 * "Como funciona" e "Sobre a rede" têm páginas próprias no menu (Publico/ComoFunciona e Publico/Sobre).
 */
export default function Feed({ demandas, filtros, metricas, iniciaisOscsDestaque }: FeedProps) {
    const carregando = useRecarregandoPagina();

    return (
        <LayoutSite>
            <Hero totalOscs={metricas.total_oscs} iniciais={iniciaisOscsDestaque} />

            <section
                className="quick-stats grid grid-cols-2 gap-y-[22px] px-2.5 py-5 sm:p-[25px] md:grid-cols-4 md:gap-y-0"
                aria-label="A rede em números"
            >
                <article>
                    <strong>{metricas.total_oscs}</strong>
                    <span className="text-[11px] sm:text-[12px]">OSCs cadastradas</span>
                </article>
                <article className="border-r-0 md:border-r">
                    <strong>{metricas.demandas_ativas}</strong>
                    <span className="text-[11px] sm:text-[12px]">Demandas ativas</span>
                </article>
                <article>
                    <strong>{metricas.demandas_concluidas}</strong>
                    <span className="text-[11px] sm:text-[12px]">Demandas concluídas</span>
                </article>
                <article>
                    <strong>{metricas.total_manifestacoes}</strong>
                    <span className="text-[11px] sm:text-[12px]">
                        Manifestações recebidas · {metricas.manifestacoes_no_mes} neste mês
                    </span>
                </article>
            </section>

            <section className="section px-5 py-[78px] sm:px-[max(7vw,24px)] sm:py-28" id="demandas">
                <div className="section-heading mx-auto mb-[38px] flex max-w-[1240px] flex-col items-start gap-5 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span className="eyebrow">Ajude agora</span>
                        <h2>Necessidades da nossa comunidade</h2>
                        <p>Cada demanda foi publicada diretamente por uma organização da rede.</p>
                    </div>
                    <div className="section-counter" aria-live="polite">
                        <strong>{demandas.meta.total}</strong>
                        <span>{demandas.meta.total === 1 ? 'demanda encontrada' : 'demandas encontradas'}</span>
                    </div>
                </div>

                <FiltrosFeed iniciais={filtros} />

                <p className="sr-only" aria-live="polite">
                    {carregando ? 'Carregando demandas…' : ''}
                </p>

                {carregando ? (
                    <EsqueletoCartoes quantidade={Math.min(Math.max(demandas.data.length, 3), 6)} />
                ) : demandas.data.length > 0 ? (
                    <>
                        <div className="mx-auto grid max-w-[1240px] grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            {demandas.data.map((demanda) => (
                                <CartaoDemanda key={demanda.id} demanda={demanda} />
                            ))}
                        </div>
                        <Paginacao pagina={demandas} rotulo="Paginação das demandas" manterRolagem={false} />
                    </>
                ) : (
                    <div className="empty-state">
                        <Icone nome="search" tamanho={36} />
                        <h3>Nenhuma demanda encontrada</h3>
                        {filtros.busca || filtros.categoria || filtros.status || filtros.urgente ? (
                            <>
                                <p>Tente mudar os filtros para ver outras oportunidades.</p>
                                <Link href={route('feed')} className="button button--secondary" preserveScroll>
                                    Limpar filtros
                                </Link>
                            </>
                        ) : (
                            <p>Assim que as organizações publicarem novas necessidades, elas aparecerão aqui.</p>
                        )}
                    </div>
                )}
            </section>
        </LayoutSite>
    );
}

function Hero({ totalOscs, iniciais }: { totalOscs: number; iniciais: string[] }) {
    return (
        <section className="hero px-5 pt-[54px] pb-[82px] sm:px-[max(7vw,24px)] md:pt-[72px]">
            <div className="hero__content">
                <div className="hero__eyebrow">
                    <span>
                        <Icone nome="location" tamanho={15} /> Camaçari, Bahia
                    </span>
                    <span className="hidden sm:inline-flex">Conectando quem transforma</span>
                </div>
                <h1>
                    Sua vontade de ajudar encontra <em>quem precisa.</em>
                </h1>
                <p className="text-[16px] sm:text-[18px]">
                    Descubra necessidades reais de organizações sociais de Camaçari e contribua com doações, tempo ou
                    conhecimento. Sem burocracia.
                </p>
                <div className="hero__actions flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="#demandas" className="button button--primary">
                        Encontrar uma causa <Icone nome="arrow" />
                    </a>
                    <Link href={route('como-funciona')} className="button button--light">
                        Como funciona
                    </Link>
                </div>
                {totalOscs > 0 && (
                    <div className="hero__trust">
                        <span className="avatar-stack" aria-hidden="true">
                            {iniciais.map((sigla) => (
                                <i key={sigla}>{sigla}</i>
                            ))}
                        </span>
                        <span>
                            <strong>{plural(totalOscs, 'organização conectada', 'organizações conectadas')}</strong>
                            em Camaçari
                        </span>
                    </div>
                )}
            </div>
        </section>
    );
}

function mesmosFiltros(a: Filtros, b: Filtros): boolean {
    return a.busca === b.busca && a.categoria === b.categoria && a.status === b.status && a.urgente === b.urgente;
}

/** Filtros aplicados sem recarregar a página; o estado fica na URL (compartilhável). */
function FiltrosFeed({ iniciais }: { iniciais: Filtros }) {
    const { referencias } = usePropsPagina();
    const [filtros, setFiltros] = useState(iniciais);

    useEffect(() => {
        // Só busca quando o formulário difere do que a página atual já mostra. Assim, abrir outra
        // página da paginação (ou o efeito rodar duas vezes no StrictMode) não refaz a busca,
        // que voltaria para a página 1.
        if (mesmosFiltros(filtros, iniciais)) {
            return;
        }

        // A busca por texto espera uma pausa na digitação; os demais filtros aplicam na hora.
        const espera = setTimeout(() => {
            const consulta = Object.fromEntries(
                Object.entries({ ...filtros, urgente: filtros.urgente ? 1 : null }).filter(([, valor]) => valor),
            );

            router.get(route('feed'), consulta, {
                only: ['demandas', 'filtros'],
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        }, 400);

        return () => clearTimeout(espera);
    }, [filtros, iniciais]);

    const alterar = <K extends keyof Filtros>(campo: K, valor: Filtros[K]) =>
        setFiltros((atual) => ({ ...atual, [campo]: valor }));

    return (
        <form
            className="filters mx-auto mb-7 grid max-w-[1240px] grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-[minmax(240px,1fr)_190px_190px_auto]"
            role="search"
            aria-label="Filtrar demandas"
            onSubmit={(evento) => evento.preventDefault()}
        >
            <label className="search-box col-span-full xl:col-auto">
                <span className="sr-only">Buscar por causa ou organização</span>
                <Icone nome="search" tamanho={19} />
                <input
                    type="search"
                    value={filtros.busca}
                    onChange={(evento) => alterar('busca', evento.target.value)}
                    placeholder="Buscar por causa ou organização"
                    maxLength={100}
                />
            </label>
            <select
                aria-label="Filtrar por categoria"
                value={filtros.categoria ?? ''}
                onChange={(evento) => alterar('categoria', evento.target.value || null)}
            >
                <option value="">Todas as categorias</option>
                {referencias.categorias.map((categoria) => (
                    <option key={categoria} value={categoria}>
                        {categoria}
                    </option>
                ))}
            </select>
            <select
                aria-label="Filtrar por status"
                value={filtros.status ?? ''}
                onChange={(evento) => alterar('status', evento.target.value || null)}
            >
                <option value="">Todos os status</option>
                {referencias.statusPublicos.map((status) => (
                    <option key={status} value={status}>
                        {referencias.status[status]}
                    </option>
                ))}
            </select>
            <label className="urgent-toggle">
                <input
                    type="checkbox"
                    className="sr-only"
                    checked={filtros.urgente}
                    onChange={(evento) => alterar('urgente', evento.target.checked)}
                />
                <Icone nome="clock" tamanho={18} /> Somente urgentes
            </label>
        </form>
    );
}

function CartaoDemanda({ demanda }: { demanda: Demanda }) {
    const prazo = formatarData(demanda.prazo);

    return (
        <article className="demand-card">
            <div className="demand-card__top">
                <div className="org-avatar" aria-hidden="true">
                    {demanda.osc?.iniciais}
                </div>
                <div className="badges">
                    {demanda.urgente && <span className="badge badge--urgent">Urgente</span>}
                    <StatusBadge status={demanda.status} />
                </div>
            </div>
            <div className="demand-card__body">
                <span className="eyebrow">{demanda.categoria}</span>
                <h3>{demanda.titulo}</h3>
                <p className="demand-card__org">{demanda.osc?.nome}</p>
                <p className="demand-card__description">{demanda.descricao}</p>
                <div className="demand-card__meta">
                    <span>
                        <Icone nome="box" tamanho={15} /> {demanda.quantidade} un.
                    </span>
                    <span>
                        <Icone nome="location" tamanho={15} /> {demanda.osc?.endereco || 'Não informado'}
                    </span>
                </div>
            </div>
            <div className="demand-card__footer">
                <span>
                    <Icone nome="calendar" tamanho={15} /> {prazo ? `Até ${prazo}` : 'Sem prazo'}
                </span>
                <Link
                    href={route('demandas.show', demanda.id)}
                    className="button button--primary"
                    aria-label={`Quero ajudar: ${demanda.titulo}`}
                    prefetch
                >
                    Quero ajudar <Icone nome="arrow" tamanho={16} />
                </Link>
            </div>
        </article>
    );
}
