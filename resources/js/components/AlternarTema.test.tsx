import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { esperarSemViolacoesDeAcessibilidade, propsCompartilhadas } from '@/test/utilitarios';
import AlternarTema from './AlternarTema';

vi.mock('@inertiajs/react', () => ({ usePage: () => ({ props: propsCompartilhadas() }) }));

describe('AlternarTema', () => {
    it('alterna sistema → claro → escuro, aplica no <html> e lembra no cookie', async () => {
        const usuario = userEvent.setup();
        const { container } = render(<AlternarTema />);
        const botao = screen.getByRole('button', { name: /Tema do sistema/ });

        await usuario.click(botao);
        expect(document.documentElement.dataset.theme).toBe('claro');
        expect(document.cookie).toContain('tema=claro');
        expect(botao).toHaveAccessibleName('Tema claro. Mudar para tema escuro');

        await usuario.click(botao);
        expect(document.documentElement.dataset.theme).toBe('escuro');

        await usuario.click(botao);
        expect(document.documentElement.dataset.theme).toBe('sistema');
        await esperarSemViolacoesDeAcessibilidade(container);
    });
});
