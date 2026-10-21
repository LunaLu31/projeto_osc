import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import { esperarSemViolacoesDeAcessibilidade } from '@/test/utilitarios';
import AlternarTema from './AlternarTema';

describe('AlternarTema', () => {
    it('alterna entre claro e escuro, aplica no <html> e lembra no cookie', async () => {
        const usuario = userEvent.setup();
        document.documentElement.dataset.theme = 'sistema';
        const { container } = render(<AlternarTema />);
        const botao = screen.getByRole('button', { name: /Mudar para tema escuro/ });

        // Sem escolha salva, parte do tema do dispositivo (claro no jsdom).
        await usuario.click(botao);
        expect(document.documentElement.dataset.theme).toBe('escuro');
        expect(document.cookie).toContain('tema=escuro');

        await usuario.click(botao);
        expect(document.documentElement.dataset.theme).toBe('claro');
        expect(document.cookie).toContain('tema=claro');

        await usuario.click(botao);
        expect(document.documentElement.dataset.theme).toBe('escuro');
        await esperarSemViolacoesDeAcessibilidade(container);
    });
});
