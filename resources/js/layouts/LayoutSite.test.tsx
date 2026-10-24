import { render, screen, waitFor, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { propsCompartilhadas } from '@/test/utilitarios';
import LayoutSite from './LayoutSite';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ href, children, ...props }: { href: string; children: ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
    router: { on: () => () => undefined },
    usePage: () => ({ props: propsCompartilhadas() }),
}));
vi.mock('ziggy-js', () => ({
    route: (nome?: string) => (nome ? `/${nome}` : { current: (padrao: string) => padrao === 'como-funciona' }),
}));

const menuAberto = () => screen.getByRole('button', { name: /menu de navegação/ }).getAttribute('aria-expanded');

describe('LayoutSite – menu de navegação', () => {
    it('abre ao passar o mouse e fecha ao tirá-lo', async () => {
        const usuario = userEvent.setup();
        render(<LayoutSite>conteúdo</LayoutSite>);
        const botao = screen.getByRole('button', { name: 'Abrir menu de navegação' });

        await usuario.hover(botao);
        expect(menuAberto()).toBe('true');

        // Passar do botão para os links não fecha o menu.
        await usuario.hover(
            within(screen.getByRole('navigation', { name: 'Navegação principal' })).getByRole('link', {
                name: /Como funciona/,
            }),
        );
        expect(menuAberto()).toBe('true');

        await usuario.unhover(botao);
        await usuario.hover(screen.getByRole('main'));
        await waitFor(() => expect(menuAberto()).toBe('false'));
    });

    it('um clique com o menu aberto pelo mouse o mantém aberto ao sair', async () => {
        const usuario = userEvent.setup();
        render(<LayoutSite>conteúdo</LayoutSite>);
        const botao = screen.getByRole('button', { name: 'Abrir menu de navegação' });

        await usuario.click(botao);
        expect(menuAberto()).toBe('true');

        await usuario.hover(screen.getByRole('main'));
        await new Promise((resolver) => setTimeout(resolver, 350));
        expect(menuAberto()).toBe('true');

        await usuario.click(screen.getByRole('button', { name: 'Fechar menu de navegação' }));
        expect(menuAberto()).toBe('false');
    });

    it('leva "Como funciona" e "Sobre a rede" para as próprias páginas e marca a atual', () => {
        render(<LayoutSite>conteúdo</LayoutSite>);
        const menu = within(screen.getByRole('navigation', { name: 'Navegação principal' }));

        expect(menu.getByRole('link', { name: /Como funciona/ })).toHaveAttribute('href', '/como-funciona');
        expect(menu.getByRole('link', { name: /Como funciona/ })).toHaveAttribute('aria-current', 'page');
        expect(menu.getByRole('link', { name: /Sobre a rede/ })).toHaveAttribute('href', '/sobre');
        expect(menu.getByRole('link', { name: /Demandas/ })).not.toHaveAttribute('aria-current');
    });
});
