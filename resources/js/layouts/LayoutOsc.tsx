import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { route } from 'ziggy-js';
import Alertas from '@/components/Alertas';
import Icone, { type NomeIcone } from '@/components/Icone';
import { usePropsPagina } from '@/lib/pagina';
import LayoutPainel from './LayoutPainel';

const secoes: {
    rota: 'osc.painel' | 'osc.demandas.index' | 'osc.contatos.index' | 'osc.senha.edit';
    ativa: string;
    icone: NomeIcone;
    rotulo: string;
}[] = [
    { rota: 'osc.painel', ativa: 'osc.painel', icone: 'home', rotulo: 'Visão geral' },
    { rota: 'osc.demandas.index', ativa: 'osc.demandas.*', icone: 'box', rotulo: 'Demandas' },
    { rota: 'osc.contatos.index', ativa: 'osc.contatos.*', icone: 'people', rotulo: 'Contatos' },
    { rota: 'osc.senha.edit', ativa: 'osc.senha.*', icone: 'key', rotulo: 'Senha' },
];

/** Layout da área da OSC: painel privado com navegação entre as seções. */
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
        >
            <nav className="painel-nav" aria-label="Seções do painel">
                {secoes.map((secao) => (
                    <Link
                        key={secao.rota}
                        href={route(secao.rota)}
                        aria-current={route().current(secao.ativa) ? 'page' : undefined}
                    >
                        <Icone nome={secao.icone} tamanho={17} /> {secao.rotulo}
                    </Link>
                ))}
            </nav>

            <Alertas />

            {children}
        </LayoutPainel>
    );
}
