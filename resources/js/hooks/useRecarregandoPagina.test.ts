import { act, renderHook } from '@testing-library/react';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { useRecarregandoPagina } from './useRecarregandoPagina';

type Ouvinte = (evento: { detail: { visit: { url: URL } } }) => void;
const { ouvintes } = vi.hoisted(() => ({ ouvintes: new Map<string, Ouvinte>() }));

vi.mock('@inertiajs/react', () => ({
    router: {
        on: (nome: string, ouvinte: Ouvinte) => {
            ouvintes.set(nome, ouvinte);
            return () => ouvintes.delete(nome);
        },
    },
}));

const visita = (caminho: string) => ({ detail: { visit: { url: new URL(caminho, window.location.origin) } } });

describe('useRecarregandoPagina', () => {
    beforeEach(() => vi.useFakeTimers());
    afterEach(() => vi.useRealTimers());

    it('sinaliza recarga da própria página após um pequeno atraso', () => {
        const { result } = renderHook(() => useRecarregandoPagina(150));

        act(() => ouvintes.get('start')!(visita(`${window.location.pathname}?categoria=Higiene`)));
        expect(result.current).toBe(false);

        act(() => vi.advanceTimersByTime(150));
        expect(result.current).toBe(true);

        act(() => ouvintes.get('finish')!(visita(window.location.pathname)));
        expect(result.current).toBe(false);
    });

    it('ignora navegações para outras páginas e respostas rápidas', () => {
        const { result } = renderHook(() => useRecarregandoPagina(150));

        act(() => ouvintes.get('start')!(visita('/outra-pagina')));
        act(() => vi.advanceTimersByTime(500));
        expect(result.current).toBe(false);

        act(() => ouvintes.get('start')!(visita(window.location.pathname)));
        act(() => ouvintes.get('finish')!(visita(window.location.pathname)));
        act(() => vi.advanceTimersByTime(500));
        expect(result.current).toBe(false);
    });
});
