import type { ReactNode } from 'react';
import { route } from 'ziggy-js';
import Alertas from '@/components/Alertas';
import type { SecaoPainel } from '@/components/MenuPainel';
import { usePropsPagina } from '@/lib/pagina';
import LayoutPainel from './LayoutPainel';

const secoes: SecaoPainel[] = [
    { rota: 'admin.painel', ativa: ['admin.painel'], icone: 'home', rotulo: 'Visão geral' },
    { rota: 'admin.oscs.create', ativa: ['admin.oscs.create'], icone: 'plus', rotulo: 'Cadastro' },
    { rota: 'admin.oscs.index', ativa: ['admin.oscs.index', 'admin.oscs.edit'], icone: 'people', rotulo: 'Rede' },
    { rota: 'admin.demandas.index', ativa: ['admin.demandas.*'], icone: 'box', rotulo: 'Moderação' },
];

/** Layout do portal administrativo: menu lateral com visão geral, cadastro, rede e moderação. */
export default function LayoutAdmin({ titulo, children }: { titulo: string; children: ReactNode }) {
    const { auth } = usePropsPagina();

    if (!auth.admin) {
        return null;
    }

    return (
        <LayoutPainel
            titulo={titulo}
            usuario={auth.admin}
            perfil="Administrador CONECTA_OSC"
            inicio={route('admin.painel')}
            sair={route('admin.logout')}
            secoes={secoes}
        >
            <Alertas />

            {children}
        </LayoutPainel>
    );
}
