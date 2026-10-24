import { Link } from '@inertiajs/react';
import { route, type RouteName } from 'ziggy-js';
import { useRotaAtual } from '@/hooks/useRotaAtual';
import Icone, { type NomeIcone } from './Icone';

export interface SecaoPainel {
    rota: RouteName;
    /** Padrões de rota que deixam a aba marcada como atual (ex.: 'osc.demandas.*'). */
    ativa: string[];
    icone: NomeIcone;
    rotulo: string;
}

/**
 * Abas do menu lateral dos painéis: cada aba é uma página própria. Em telas pequenas só os
 * ícones ficam à vista; o rótulo continua sendo o nome do link e vira a dica (title).
 */
export default function MenuPainel({ secoes }: { secoes: SecaoPainel[] }) {
    const rotaAtual = useRotaAtual();

    return (
        <nav className="painel-nav" aria-label="Seções do painel">
            {secoes.map((secao) => (
                <Link
                    key={secao.rota}
                    href={route(secao.rota)}
                    aria-current={rotaAtual(...secao.ativa) ? 'page' : undefined}
                    title={secao.rotulo}
                >
                    <Icone nome={secao.icone} tamanho={19} />
                    <span className="painel-nav__rotulo">{secao.rotulo}</span>
                </Link>
            ))}
        </nav>
    );
}
