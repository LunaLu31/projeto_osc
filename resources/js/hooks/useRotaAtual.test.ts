import { renderHook } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import { propsCompartilhadas } from '@/test/utilitarios';
import { useRotaAtual } from './useRotaAtual';

const { props } = vi.hoisted(() => ({ props: { atual: {} as object } }));
vi.mock('@inertiajs/react', () => ({ usePage: () => ({ props: props.atual }) }));

// Ziggy de verdade (sem mock): a URL vem como string do Laravel, como em HandleInertiaRequests.
const naPagina = (location: string) => {
    props.atual = propsCompartilhadas({
        ziggy: {
            url: 'http://127.0.0.1:8000',
            port: 8000,
            defaults: {},
            location,
            routes: {
                'osc.demandas.index': { uri: 'osc/demandas', methods: ['GET', 'HEAD'] },
                'osc.demandas.edit': {
                    uri: 'osc/demandas/{demanda}/editar',
                    methods: ['GET', 'HEAD'],
                    parameters: ['demanda'],
                },
                'osc.contatos.index': { uri: 'osc/contatos', methods: ['GET', 'HEAD'] },
            },
        },
    });
};

describe('useRotaAtual', () => {
    it('reconhece a rota da página pela URL das props, com curinga e mais de um padrão', () => {
        naPagina('http://127.0.0.1:8000/osc/demandas/7/editar');
        const { result } = renderHook(() => useRotaAtual());

        expect(result.current('osc.demandas.*')).toBe(true);
        expect(result.current('osc.contatos.index')).toBe(false);
        expect(result.current('osc.contatos.index', 'osc.demandas.edit')).toBe(true);
    });

    it('acompanha a navegação: vale a URL da página nova, não a da primeira carga', () => {
        naPagina('http://127.0.0.1:8000/osc/demandas');
        const { result, rerender } = renderHook(() => useRotaAtual());
        expect(result.current('osc.demandas.index')).toBe(true);

        naPagina('http://127.0.0.1:8000/osc/contatos');
        rerender();
        expect(result.current('osc.demandas.index')).toBe(false);
        expect(result.current('osc.contatos.index')).toBe(true);
    });
});
