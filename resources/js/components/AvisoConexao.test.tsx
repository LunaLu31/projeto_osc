import { act, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import AvisoConexao from './AvisoConexao';

type Ouvinte = (evento: { preventDefault: () => void }) => void;
const { ouvintes } = vi.hoisted(() => ({ ouvintes: new Map<string, Ouvinte>() }));

vi.mock('@inertiajs/react', () => ({
    router: {
        on: (nome: string, ouvinte: Ouvinte) => {
            ouvintes.set(nome, ouvinte);
            return () => ouvintes.delete(nome);
        },
    },
}));

describe('AvisoConexao', () => {
    it('avisa quando a internet cai e some quando ela volta', () => {
        render(<AvisoConexao />);
        expect(screen.queryByRole('alert')).not.toBeInTheDocument();

        act(() => window.dispatchEvent(new Event('offline')));
        expect(screen.getByRole('alert')).toHaveTextContent('Você está sem internet');

        act(() => window.dispatchEvent(new Event('online')));
        expect(screen.queryByRole('alert')).not.toBeInTheDocument();
    });

    it('trata falhas de rede do Inertia e pode ser fechado', async () => {
        const usuario = userEvent.setup();
        render(<AvisoConexao />);
        const preventDefault = vi.fn();

        act(() => ouvintes.get('networkError')!({ preventDefault }));

        expect(preventDefault).toHaveBeenCalled();
        expect(screen.getByRole('alert')).toHaveTextContent('Não foi possível falar com o servidor');

        await usuario.click(screen.getByRole('button', { name: 'Fechar aviso' }));
        expect(screen.queryByRole('alert')).not.toBeInTheDocument();
    });
});
