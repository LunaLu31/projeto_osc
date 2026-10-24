import TabelaDemandasRede from '@/components/admin/TabelaDemandasRede';
import TituloPainel from '@/components/TituloPainel';
import { useRecarregandoPagina } from '@/hooks/useRecarregandoPagina';
import LayoutAdmin from '@/layouts/LayoutAdmin';
import type { Demanda, Paginado } from '@/types';

/** Aba "Moderação" do administrador: intervenção em qualquer demanda da rede (RF07). */
export default function Moderacao({ demandas }: { demandas: Paginado<Demanda> }) {
    const carregando = useRecarregandoPagina();

    return (
        <LayoutAdmin titulo="Moderação das demandas">
            <TituloPainel
                rotulo="Moderação"
                titulo="Demandas da rede"
                descricao="Altere o status ou remova demandas publicadas por qualquer OSC."
            />

            <TabelaDemandasRede demandas={demandas} carregando={carregando} />
        </LayoutAdmin>
    );
}
