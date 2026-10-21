import { Link, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
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

/** Página inicial: apresentação, métricas e listagem pública de demandas (RF04). */
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

            <ComoFunciona />
            <Sobre />

            <section className="cta-section px-[22px] py-14 sm:px-6 sm:py-[76px]">
                <span className="eyebrow">Faça parte desta rede</span>
                <h2>Existe uma causa esperando por você.</h2>
                <p>
                    Doe materiais, compartilhe conhecimento ou ofereça seu tempo. Toda contribuição começa com uma
                    conexão.
                </p>
                <a href="#demandas" className="button button--light">
                    Ver demandas abertas <Icone nome="arrow" />
                </a>
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
                <div className="hero__actions flex flex-col gap-3 sm:flex-row">
                    <a href="#demandas" className="button button--primary">
                        Encontrar uma causa <Icone nome="arrow" />
                    </a>
                    <a href="#como-funciona" className="button button--light">
                        Como funciona
                    </a>
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

/** Filtros aplicados sem recarregar a página; o estado fica na URL (compartilhável). */
function FiltrosFeed({ iniciais }: { iniciais: Filtros }) {
    const { referencias } = usePropsPagina();
    const [filtros, setFiltros] = useState(iniciais);
    const primeiraRenderizacao = useRef(true);

    useEffect(() => {
        if (primeiraRenderizacao.current) {
            primeiraRenderizacao.current = false;
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
    }, [filtros]);

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

function ComoFunciona() {
    const passos = [
        {
            icone: 'search',
            titulo: 'Encontre uma causa',
            texto: 'Use os filtros para descobrir necessidades que combinam com a forma como você quer ajudar.',
        },
        {
            icone: 'heart',
            titulo: 'Manifeste seu interesse',
            texto: 'Preencha um formulário rápido. Não precisa criar uma conta nem expor seus dados publicamente.',
        },
        {
            icone: 'people',
            titulo: 'Conecte-se com a OSC',
            texto: 'A organização recebe seu contato e combina diretamente os próximos passos com você.',
        },
    ] as const;

    return (
        <section className="section section--tinted px-5 py-[78px] sm:px-[max(7vw,24px)] sm:py-28" id="como-funciona">
            <div className="section-heading mx-auto mb-[38px] flex max-w-[1240px] flex-col items-start gap-5 text-center md:flex-row md:items-end md:justify-center">
                <div>
                    <span className="eyebrow">Simples e transparente</span>
                    <h2>Ajudar ficou mais fácil</h2>
                    <p>Em três passos, sua vontade se transforma em impacto real.</p>
                </div>
            </div>
            <div className="steps mx-auto grid max-w-[1100px] grid-cols-1 gap-[42px] md:grid-cols-3 md:gap-[52px]">
                {passos.map((passo, indice) => (
                    <article key={passo.titulo}>
                        <span className="step-number">{String(indice + 1).padStart(2, '0')}</span>
                        <div className="step-icon">
                            <Icone nome={passo.icone} tamanho={29} />
                        </div>
                        <h3>{passo.titulo}</h3>
                        <p className="mx-auto max-w-[420px] md:mx-0 md:max-w-none">{passo.texto}</p>
                    </article>
                ))}
            </div>
        </section>
    );
}

function Sobre() {
    return (
        <section className="section px-5 py-[78px] sm:px-[max(7vw,24px)] sm:py-28" id="sobre">
            <div className="about-section mx-auto grid max-w-[1280px] grid-cols-1 items-center gap-[45px] md:grid-cols-[1fr_0.8fr] md:gap-25">
                <div className="about-section__copy">
                    <span className="eyebrow">Uma rede feita em Camaçari</span>
                    <h2>Informação centralizada. Impacto multiplicado.</h2>
                    <p>
                        A Redcam aproxima organizações sociais, apoiadores e a UOSCC em um ambiente confiável. Aqui,
                        necessidades reais não se perdem no fluxo das redes sociais.
                    </p>
                    <ul>
                        {[
                            'Demandas organizadas e atualizadas',
                            'Contatos protegidos pela LGPD',
                            'Acompanhamento do status de cada causa',
                        ].map((item) => (
                            <li key={item}>
                                <Icone nome="check" /> {item}
                            </li>
                        ))}
                    </ul>
                </div>
                <div className="about-section__quote px-7 py-[38px] sm:p-[55px]">
                    <Icone nome="spark" tamanho={28} />
                    <blockquote>“Quando a informação chega à pessoa certa, a solidariedade vira ação.”</blockquote>
                    <p>Rede UOSCC · Camaçari</p>
                </div>
            </div>
        </section>
    );
}
