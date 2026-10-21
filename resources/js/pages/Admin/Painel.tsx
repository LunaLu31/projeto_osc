import FormularioCadastroOsc from '@/components/admin/FormularioCadastroOsc';
import TabelaDemandasRede from '@/components/admin/TabelaDemandasRede';
import TabelaOscs from '@/components/admin/TabelaOscs';
import Icone from '@/components/Icone';
import MetricaCard from '@/components/MetricaCard';
import TituloPainel from '@/components/TituloPainel';
import { useRecarregandoPagina } from '@/hooks/useRecarregandoPagina';
import LayoutAdmin from '@/layouts/LayoutAdmin';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda, MetricasRede, Osc, Paginado, StatusDemanda } from '@/types';

interface PainelProps {
    metricas: MetricasRede;
    oscs: Paginado<Osc>;
    demandas: Paginado<Demanda>;
}

/** Painel do administrador: métricas globais e gestão da rede (RF02, RF07, RF09). */
export default function Painel({ metricas, oscs, demandas }: PainelProps) {
    const { auth, flash, referencias } = usePropsPagina();
    const carregando = useRecarregandoPagina();

    const resumoStatus = (Object.keys(referencias.status) as StatusDemanda[])
        .map((status) => `${referencias.status[status]}: ${metricas.demandas_por_status[status]}`)
        .join(' · ');
    const totalDemandas = Object.values(metricas.demandas_por_status).reduce((soma, total) => soma + total, 0);

    return (
        <LayoutAdmin titulo="Painel administrativo">
            {flash.senhaInicial && (
                <div className="alert alert--warning" role="status">
                    <Icone nome="key" />
                    <span>
                        Senha inicial da OSC (exibida uma única vez):{' '}
                        <strong className="select-all">{flash.senhaInicial}</strong>. Repasse por um canal seguro e
                        oriente a troca no primeiro acesso.
                    </span>
                </div>
            )}

            <TituloPainel
                rotulo="Painel administrativo"
                titulo={`Olá, ${auth.admin?.nome}`}
                descricao="Acompanhe todas as OSCs e demandas da rede."
            />

            <section className="metric-grid my-8 grid grid-cols-1 gap-4 md:grid-cols-3" aria-label="Métricas da rede">
                <MetricaCard icone="people" cor="blue" valor={metricas.total_oscs} rotulo="OSCs cadastradas" />
                <MetricaCard
                    icone="box"
                    cor="green"
                    valor={totalDemandas}
                    rotulo="Demandas na rede"
                    detalhe={resumoStatus}
                />
                <MetricaCard
                    icone="heart"
                    cor="orange"
                    valor={metricas.total_manifestacoes}
                    rotulo="Manifestações de interesse"
                />
            </section>

            <div className="admin-grid grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,0.75fr)_minmax(0,1.25fr)]">
                <FormularioCadastroOsc />
                <TabelaOscs oscs={oscs} carregando={carregando} />
            </div>

            <TabelaDemandasRede demandas={demandas} carregando={carregando} />
        </LayoutAdmin>
    );
}
