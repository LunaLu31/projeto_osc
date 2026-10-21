import { render, screen, waitFor, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import type { ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { propsCompartilhadas } from '@/test/utilitarios';
import LayoutSite from './LayoutSite';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ href, children }: { href: string; children: ReactNode }) => <a href={href}>{children}</a>,
    router: { on: () => () => undefined },
    usePage: () => ({ props: propsCompartilhadas() }),
}));
vi.mock('ziggy-js', () => ({ route: (nome: string) => `/${nome}` }));

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
});
