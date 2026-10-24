import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import { esperarSemViolacoesDeAcessibilidade } from '@/test/utilitarios';
import Campo from './Campo';

describe('Campo', () => {
    it('associa o rótulo ao campo e exibe o erro de validação do Laravel', async () => {
        const { container } = render(
            <Campo rotulo="E-mail" erro="Informe um e-mail válido.">
                <input type="email" />
            </Campo>,
        );

        expect(screen.getByLabelText(/E-mail/)).toBeInTheDocument();
        expect(screen.getByRole('alert')).toHaveTextContent('Informe um e-mail válido.');
        expect(container.querySelector('.field')).toHaveClass('field--invalid');
        await esperarSemViolacoesDeAcessibilidade(container);
    });

    it('indica campos opcionais e mostra a dica', () => {
        render(
            <Campo rotulo="Mensagem" opcional dica="Até 2000 caracteres.">
                <textarea />
            </Campo>,
        );

        expect(screen.getByText('(opcional)')).toBeInTheDocument();
        expect(screen.getByText('Até 2000 caracteres.')).toBeInTheDocument();
        expect(screen.queryByRole('alert')).not.toBeInTheDocument();
    });
});
