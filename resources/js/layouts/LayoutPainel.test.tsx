import { render, screen, within } from '@testing-library/react';
import type { ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';
import type { SecaoPainel } from '@/components/MenuPainel';
import { esperarSemViolacoesDeAcessibilidade, propsCompartilhadas } from '@/test/utilitarios';
import LayoutPainel from './LayoutPainel';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ href, children, ...props }: { href: string; children: ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
    usePage: () => ({ props: propsCompartilhadas() }),
}));
vi.mock('ziggy-js', () => ({
    route: (nome?: string) => (nome ? `/${nome}` : { current: (padrao: string) => padrao === 'admin.painel' }),
}));

const secoes: SecaoPainel[] = [
    { rota: 'admin.oscs.create', ativa: ['admin.oscs.create'], icone: 'plus', rotulo: 'Cadastro' },
    { rota: 'admin.painel', ativa: ['admin.painel'], icone: 'people', rotulo: 'Rede' },
    { rota: 'admin.demandas.index', ativa: ['admin.demandas.*'], icone: 'box', rotulo: 'Moderação' },
];

describe('LayoutPainel – menu lateral', () => {
    it('mostra sempre as abas, marca a atual e não tem botão de menu', async () => {
        const { container } = render(
            <LayoutPainel
                titulo="Painel"
                usuario={{ nome: 'Admin', iniciais: 'AD' }}
                perfil="Administrador"
                inicio="/"
                sair="/sair"
                secoes={secoes}
            >
                conteúdo
            </LayoutPainel>,
        );
        const menu = within(screen.getByRole('navigation', { name: 'Seções do painel' }));

        expect(menu.getAllByRole('link').map((link) => link.textContent)).toEqual(['Cadastro', 'Rede', 'Moderação']);
        expect(menu.getByRole('link', { name: 'Rede' })).toHaveAttribute('aria-current', 'page');
        // Só com ícones em telas pequenas, o nome da aba vira a dica.
        expect(menu.getByRole('link', { name: 'Moderação' })).toHaveAttribute('title', 'Moderação');
        expect(screen.queryByRole('button', { name: /menu/i })).not.toBeInTheDocument();
        await esperarSemViolacoesDeAcessibilidade(container);
    });
});
