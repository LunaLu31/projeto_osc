import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { esperarSemViolacoesDeAcessibilidade } from '@/test/utilitarios';
import BotaoExcluir from './BotaoExcluir';

const { excluir } = vi.hoisted(() => ({ excluir: vi.fn() }));
vi.mock('@inertiajs/react', () => ({ router: { delete: excluir } }));

const props = {
    url: '/osc/demandas/7',
    titulo: 'Excluir a demanda “Cestas básicas”?',
    descricao: 'As manifestações recebidas também serão apagadas.',
};

describe('BotaoExcluir', () => {
    it('só exclui depois da confirmação no diálogo', async () => {
        const usuario = userEvent.setup();
        const { container } = render(<BotaoExcluir {...props} />);

        await usuario.click(screen.getByRole('button', { name: 'Excluir' }));

        const dialogo = container.querySelector('dialog')!;
        expect(dialogo.open).toBe(true);
        expect(dialogo).toHaveAccessibleName(props.titulo);
        expect(dialogo).toHaveAccessibleDescription(props.descricao);
        expect(excluir).not.toHaveBeenCalled();

        const [, confirmar] = screen.getAllByRole('button', { name: 'Excluir' });
        await usuario.click(confirmar);

        expect(excluir).toHaveBeenCalledWith('/osc/demandas/7', expect.objectContaining({ preserveScroll: true }));
        await esperarSemViolacoesDeAcessibilidade(container);
    });

    it('não exclui quando a pessoa cancela', async () => {
        const usuario = userEvent.setup();
        const { container } = render(<BotaoExcluir {...props} />);

        await usuario.click(screen.getByRole('button', { name: 'Excluir' }));
        await usuario.click(screen.getByRole('button', { name: 'Cancelar' }));

        expect(container.querySelector('dialog')!.open).toBe(false);
        expect(excluir).not.toHaveBeenCalled();
    });
});
