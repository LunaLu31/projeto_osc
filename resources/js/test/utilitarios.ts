import axe from 'axe-core';
import { expect } from 'vitest';
import type { PropsCompartilhadas } from '@/types';

/**
 * Verifica acessibilidade com o axe-core (WCAG 2.1 A/AA).
 * Contraste é auditado no navegador real (o jsdom não calcula cores).
 */
export async function esperarSemViolacoesDeAcessibilidade(elemento: Element): Promise<void> {
    const resultado = await axe.run(elemento, {
        runOnly: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'],
        rules: { 'color-contrast': { enabled: false } },
    });

    expect(resultado.violations.map((v) => `${v.id}: ${v.help}`)).toEqual([]);
}

/** Props compartilhadas mínimas para componentes que usam usePropsPagina(). */
export function propsCompartilhadas(extra: Partial<PropsCompartilhadas> = {}): PropsCompartilhadas {
    return {
        app: { nome: 'Redcam' },
        auth: { osc: null, admin: null },
        flash: { status: null, erro: null, senhaInicial: null },
        tema: 'sistema',
        referencias: {
            categorias: ['Alimentos', 'Higiene'],
            status: { Aberta: 'Aberta', Parcial: 'Parcialmente atendida', Concluída: 'Concluída' },
            statusPublicos: ['Aberta', 'Parcial'],
            tiposInteresse: ['Apoiador/Doação', 'Voluntário', 'Aluno'],
        },
        ziggy: { url: 'http://localhost', port: null, defaults: {}, routes: {} },
        errors: {},
        ...extra,
    };
}
