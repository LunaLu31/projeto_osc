import type { ReactNode } from 'react';
import { route } from 'ziggy-js';
import Alertas from '@/components/Alertas';
import type { SecaoPainel } from '@/components/MenuPainel';
import { usePropsPagina } from '@/lib/pagina';
import LayoutPainel from './LayoutPainel';

const secoes: SecaoPainel[] = [
    { rota: 'osc.painel', ativa: ['osc.painel'], icone: 'home', rotulo: 'Visão geral' },
    { rota: 'osc.demandas.index', ativa: ['osc.demandas.*'], icone: 'box', rotulo: 'Demandas' },
    { rota: 'osc.contatos.index', ativa: ['osc.contatos.*'], icone: 'people', rotulo: 'Contatos' },
    { rota: 'osc.senha.edit', ativa: ['osc.senha.*'], icone: 'key', rotulo: 'Senha' },
];

/** Layout da área da OSC: menu lateral com visão geral, demandas, contatos e senha. */
export default function LayoutOsc({ titulo, children }: { titulo: string; children: ReactNode }) {
    const { auth } = usePropsPagina();

    if (!auth.osc) {
        return null;
    }

    return (
        <LayoutPainel
            titulo={titulo}
            usuario={auth.osc}
            perfil="Perfil da OSC"
            inicio={route('osc.painel')}
            sair={route('osc.logout')}
            secoes={secoes}
        >
            <Alertas />

            {children}
        </LayoutPainel>
    );
}
