import { useState } from 'react';
import { usePropsPagina } from '@/lib/pagina';
import type { Tema } from '@/types';
import Icone, { type NomeIcone } from './Icone';

const ORDEM: Tema[] = ['sistema', 'claro', 'escuro'];
const ROTULOS: Record<Tema, string> = { sistema: 'do sistema', claro: 'claro', escuro: 'escuro' };
const ICONES: Record<Tema, NomeIcone> = { sistema: 'monitor', claro: 'sun', escuro: 'moon' };
const UM_ANO = 60 * 60 * 24 * 365;

/** Alterna o tema (sistema → claro → escuro) e lembra a escolha no cookie `tema`. */
export default function AlternarTema() {
    const { tema: temaInicial } = usePropsPagina();
    const [tema, setTema] = useState<Tema>(temaInicial);
    const proximo = ORDEM[(ORDEM.indexOf(tema) + 1) % ORDEM.length];

    const alternar = () => {
        setTema(proximo);
        document.documentElement.dataset.theme = proximo;
        document.cookie = `tema=${proximo}; path=/; max-age=${UM_ANO}; samesite=lax`;
    };

    return (
        <button
            type="button"
            className="menu-button"
            onClick={alternar}
            aria-label={`Tema ${ROTULOS[tema]}. Mudar para tema ${ROTULOS[proximo]}`}
            title={`Tema ${ROTULOS[tema]}`}
        >
            <Icone nome={ICONES[tema]} />
        </button>
    );
}
