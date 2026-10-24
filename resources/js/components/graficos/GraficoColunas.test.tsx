import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import { esperarSemViolacoesDeAcessibilidade } from '@/test/utilitarios';
import GraficoBarras from './GraficoBarras';
import GraficoColunas from './GraficoColunas';

const dados = [
    { chave: '2026-09', rotulo: 'set.', rotuloCompleto: 'setembro de 2026', valor: 5 },
    { chave: '2026-10', rotulo: 'out.', rotuloCompleto: 'outubro de 2026', valor: 2 },
    { chave: '2026-11', rotulo: 'nov.', rotuloCompleto: 'novembro de 2026', valor: 0 },
];
const contatos = (total: number) => `${total} ${total === 1 ? 'contato' : 'contatos'}`;

describe('GraficoColunas', () => {
    it('mostra a dica no foco do teclado e no mouse, sem depender dela', async () => {
        const usuario = userEvent.setup();
        const { container } = render(
            <GraficoColunas
                titulo="Contatos por mês"
                dados={dados}
                colunas={['Mês', 'Contatos']}
                formatarValor={contatos}
            />,
        );

        const grafico = screen.getByRole('group', { name: 'Contatos por mês' });
        expect(
            within(grafico)
                .getAllByRole('img')
                .map((coluna) => coluna.getAttribute('aria-label')),
        ).toEqual(['setembro de 2026: 5 contatos', 'outubro de 2026: 2 contatos', 'novembro de 2026: 0 contatos']);
        expect(container.querySelector('.grafico__dica')).toBeNull();

        await usuario.tab();
        expect(container.querySelector('.grafico__dica')).toHaveTextContent('5 contatossetembro de 2026');

        await usuario.hover(within(grafico).getByRole('img', { name: /outubro/ }));
        expect(container.querySelector('.grafico__dica')).toHaveTextContent('2 contatosoutubro de 2026');

        // Equivalente em tabela, sempre disponível.
        const tabela = screen.getByRole('table', { name: 'Contatos por mês', hidden: true });
        expect(within(tabela).getAllByRole('row', { hidden: true })).toHaveLength(4);

        await esperarSemViolacoesDeAcessibilidade(container);
    });

    it('rotula diretamente só o mês atual e o maior valor', () => {
        const { container } = render(
            <GraficoColunas titulo="Contatos" dados={dados} colunas={['Mês', 'Contatos']} formatarValor={contatos} />,
        );

        expect([...container.querySelectorAll('.grafico__valor')].map((rotulo) => rotulo.textContent)).toEqual([
            '5',
            '0',
        ]);
        expect(container.querySelectorAll('.grafico__coluna')).toHaveLength(2);
    });
});

describe('GraficoBarras', () => {
    it('lista cada categoria com o valor legível por leitores de tela', async () => {
        const { container } = render(
            <GraficoBarras
                titulo="Contatos por tipo"
                itens={[
                    { chave: 'a', rotulo: 'Voluntário', valor: 3 },
                    { chave: 'b', rotulo: 'Aluno', valor: 0 },
                ]}
                formatarValor={contatos}
            />,
        );

        const itens = within(screen.getByRole('list', { name: 'Contatos por tipo' })).getAllByRole('listitem');
        expect(itens.map((item) => item.textContent)).toEqual(['Voluntário: 3 contatos3', 'Aluno: 0 contatos0']);

        await esperarSemViolacoesDeAcessibilidade(container);
    });
});
