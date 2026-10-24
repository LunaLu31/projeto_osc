import { act, fireEvent, render, screen } from '@testing-library/react';
import { StrictMode, type ReactNode } from 'react';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import type { Demanda, MetricasRede, Paginado } from '@/types';
import { propsCompartilhadas } from '@/test/utilitarios';
import Feed from './Feed';

const { visitar } = vi.hoisted(() => ({ visitar: vi.fn() }));
vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ href, children, className }: { href: string; children: ReactNode; className?: string }) => (
        <a href={href} className={className}>
            {children}
        </a>
    ),
    router: { get: visitar, on: () => () => undefined },
    usePage: () => ({ props: propsCompartilhadas() }),
}));
vi.mock('ziggy-js', () => ({
    route: (nome?: string) => (nome ? `/${nome}` : { current: () => false }),
}));

const metricas: MetricasRede = {
    total_oscs: 1,
    demandas_por_status: { Aberta: 0, Parcial: 0, Concluída: 0 },
    demandas_ativas: 0,
    demandas_concluidas: 0,
    total_manifestacoes: 0,
    manifestacoes_no_mes: 0,
};

/** Segunda de três páginas, sem cartões (o comportamento testado é o dos filtros). */
const segundaPagina: Paginado<Demanda> = {
    data: [],
    links: {
        first: '/?page=1#demandas',
        last: '/?page=3#demandas',
        prev: '/?page=1#demandas',
        next: '/?page=3#demandas',
    },
    meta: { current_page: 2, last_page: 3, per_page: 12, total: 30, from: 13, to: 24, links: [], path: '/' },
};

const filtros = { busca: '', categoria: null, status: null, urgente: false };

const renderizar = () =>
    render(
        <StrictMode>
            <Feed demandas={segundaPagina} filtros={filtros} metricas={metricas} iniciaisOscsDestaque={['OE']} />
        </StrictMode>,
    );

describe('Feed – filtros', () => {
    beforeEach(() => vi.useFakeTimers());
    afterEach(() => vi.useRealTimers());

    it('abrir uma página (ex.: "Próxima") não dispara uma busca que volta para a página 1', () => {
        renderizar();

        act(() => vi.advanceTimersByTime(1000));

        expect(visitar).not.toHaveBeenCalled();
    });

    it('mudar um filtro busca de novo a partir da primeira página', () => {
        renderizar();

        fireEvent.change(screen.getByRole('searchbox', { name: 'Buscar por causa ou organização' }), {
            target: { value: 'cestas' },
        });
        act(() => vi.advanceTimersByTime(1000));

        expect(visitar).toHaveBeenCalledTimes(1);
        expect(visitar).toHaveBeenCalledWith('/feed', { busca: 'cestas' }, expect.objectContaining({ replace: true }));
    });
});
