import { render, screen } from '@testing-library/react';
import type { ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { esperarSemViolacoesDeAcessibilidade } from '@/test/utilitarios';
import type { Paginado } from '@/types';
import Paginacao from './Paginacao';

vi.mock('@inertiajs/react', () => ({
    Link: ({
        href,
        children,
        className,
        rel,
    }: {
        href: string;
        children: ReactNode;
        className?: string;
        rel?: string;
    }) => (
        <a href={href} className={className} rel={rel}>
            {children}
        </a>
    ),
}));

function pagina(atual: number, ultima: number): Paginado<number> {
    return {
        data: [1, 2, 3],
        links: {
            first: '/?page=1',
            last: `/?page=${ultima}`,
            prev: atual > 1 ? `/?page=${atual - 1}` : null,
            next: atual < ultima ? `/?page=${atual + 1}` : null,
        },
        meta: {
            current_page: atual,
            last_page: ultima,
            per_page: 3,
            total: ultima * 3,
            from: 1,
            to: 3,
            links: [],
            path: '/',
        },
    };
}

describe('Paginacao', () => {
    it('não aparece quando há uma página só', () => {
        const { container } = render(<Paginacao pagina={pagina(1, 1)} rotulo="Paginação" />);
        expect(container).toBeEmptyDOMElement();
    });

    it('mostra a posição e desabilita "Anterior" na primeira página', async () => {
        const { container } = render(<Paginacao pagina={pagina(1, 3)} rotulo="Paginação das demandas" />);

        expect(screen.getByRole('navigation', { name: 'Paginação das demandas' })).toHaveTextContent('Página 1 de 3');
        expect(screen.getByText('Anterior')).toHaveAttribute('aria-disabled', 'true');
        expect(screen.getByRole('link', { name: 'Próxima' })).toHaveAttribute('href', '/?page=2');
        await esperarSemViolacoesDeAcessibilidade(container);
    });
});
