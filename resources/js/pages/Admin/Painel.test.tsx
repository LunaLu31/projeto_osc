import { render, screen, within } from '@testing-library/react';
import type { ComponentProps, ReactNode } from 'react';
import { describe, expect, it, vi } from 'vitest';
import type { Demanda, MetricasAdmin, Osc } from '@/types';
import { esperarSemViolacoesDeAcessibilidade, propsCompartilhadas } from '@/test/utilitarios';
import Painel from './Painel';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ href, children, ...props }: { href: string; children: ReactNode }) => (
        <a href={href} {...props}>
            {children}
        </a>
    ),
    router: { on: () => () => undefined },
    usePage: () => ({
        props: propsCompartilhadas({ auth: { osc: null, admin: { id: 1, nome: 'Ana', iniciais: 'AN' } } }),
    }),
}));
vi.mock('ziggy-js', () => ({
    route: (nome?: string) => (nome ? `/${nome}` : { current: (padrao: string) => padrao === 'admin.painel' }),
}));

const metricas = (extra: Partial<MetricasAdmin> = {}): MetricasAdmin => ({
    total_oscs: 0,
    demandas_por_status: { Aberta: 0, Parcial: 0, Concluída: 0 },
    demandas_ativas: 0,
    demandas_concluidas: 0,
    total_manifestacoes: 0,
    manifestacoes_no_mes: 0,
    demandas_ativas_por_categoria: { Alimentos: 0, Higiene: 0 },
    manifestacoes_por_mes: [
        { mes: '2026-09', total: 0 },
        { mes: '2026-10', total: 0 },
    ],
    ...extra,
});

const vazio: ComponentProps<typeof Painel> = {
    metricas: metricas(),
    prazosVencidos: { total: 0, itens: [] },
    oscsSemDemandasAtivas: { total: 0, itens: [] },
};

describe('Admin/Painel – visão geral', () => {
    it('sem dados mostra os estados vazios', async () => {
        const { container } = render(<Painel {...vazio} />);

        expect(screen.getByRole('heading', { level: 1, name: 'Olá, Ana' })).toBeInTheDocument();
        expect(screen.getByText('Nenhuma demanda ativa está com o prazo vencido.')).toBeInTheDocument();
        expect(screen.getByText('Todas as OSCs têm ao menos uma demanda ativa.')).toBeInTheDocument();
        await esperarSemViolacoesDeAcessibilidade(container);
    });

    it('mostra os gráficos, só as categorias com demanda e as listas de atenção', async () => {
        const { container } = render(
            <Painel
                {...vazio}
                metricas={metricas({
                    total_oscs: 2,
                    demandas_por_status: { Aberta: 2, Parcial: 1, Concluída: 1 },
                    demandas_ativas: 3,
                    demandas_concluidas: 1,
                    total_manifestacoes: 4,
                    demandas_ativas_por_categoria: { Alimentos: 3, Higiene: 0 },
                    manifestacoes_por_mes: [
                        { mes: '2026-09', total: 1 },
                        { mes: '2026-10', total: 3 },
                    ],
                })}
                prazosVencidos={{
                    total: 1,
                    itens: [{ id: 7, titulo: 'Cestas básicas', status: 'Aberta', prazo: '2026-10-01' } as Demanda],
                }}
                oscsSemDemandasAtivas={{
                    total: 1,
                    itens: [{ id: 2, nome: 'OSC Beta', email: 'beta@example.com', total_demandas: 0 } as Osc],
                }}
            />,
        );

        expect(screen.queryByRole('region', { name: /Indicadores/ })).not.toBeInTheDocument();
        expect(screen.getByRole('group', { name: /Manifestações de interesse na rede por mês/ })).toBeInTheDocument();
        const categorias = within(screen.getByRole('list', { name: 'Demandas ativas por categoria' }));
        expect(categorias.getAllByRole('listitem')).toHaveLength(1);
        expect(screen.getByText('venceu em 01/10/2026')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Moderar 1 demanda' })).toHaveAttribute(
            'href',
            '/admin.demandas.index',
        );
        expect(screen.getByRole('link', { name: 'Ver dados de OSC Beta' })).toHaveAttribute('href', '/admin.oscs.edit');
        await esperarSemViolacoesDeAcessibilidade(container);
    });
});
