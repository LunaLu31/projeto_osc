import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import TabelaOscs from '@/components/admin/TabelaOscs';
import Icone from '@/components/Icone';
import TituloPainel from '@/components/TituloPainel';
import { useRecarregandoPagina } from '@/hooks/useRecarregandoPagina';
import LayoutAdmin from '@/layouts/LayoutAdmin';
import { usePropsPagina } from '@/lib/pagina';
import type { Osc, Paginado } from '@/types';

/** Aba "Rede" do administrador: OSCs cadastradas, com edição e exclusão (RF09). */
export default function Rede({ oscs }: { oscs: Paginado<Osc> }) {
    const { flash } = usePropsPagina();
    const carregando = useRecarregandoPagina();

    return (
        <LayoutAdmin titulo="Rede">
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
                rotulo="Rede"
                titulo="OSCs cadastradas"
                descricao="Edite os dados e o acesso das organizações da rede."
                acao={
                    <Link href={route('admin.oscs.create')} className="button button--primary">
                        <Icone nome="plus" /> Cadastrar OSC
                    </Link>
                }
            />

            <div className="mt-8">
                <TabelaOscs oscs={oscs} carregando={carregando} />
            </div>
        </LayoutAdmin>
    );
}
