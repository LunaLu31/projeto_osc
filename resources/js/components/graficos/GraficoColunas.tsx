import { useState } from 'react';
import { cn } from '@/lib/classes';
import { escalaLimpa } from '@/lib/graficos';
import TabelaGrafico from './TabelaGrafico';

export interface PontoGrafico {
    chave: string;
    /** Rótulo curto do eixo (ex.: "mai."). */
    rotulo: string;
    /** Rótulo da dica, da tabela e dos leitores de tela (ex.: "maio de 2026"). */
    rotuloCompleto: string;
    valor: number;
}

interface GraficoColunasProps {
    titulo: string;
    dados: PontoGrafico[];
    /** Cabeçalhos da tabela equivalente: [categoria, valor]. */
    colunas: [string, string];
    formatarValor: (valor: number) => string;
}

// Geometria em pixels no eixo vertical (altura fixa); no horizontal, porcentagens (largura fluida).
const TOPO = 24;
const ALTURA = 160;
const BASE = 28;
const ALTURA_TOTAL = TOPO + ALTURA + BASE;
const LARGURA_COLUNA = 24;
const RAIO = 4;

/** Coluna centrada em x = 0 e apoiada na linha de base (y = 0): cantos arredondados só no topo. */
function caminhoColuna(altura: number): string {
    const meia = LARGURA_COLUNA / 2;
    const r = Math.min(RAIO, altura);

    return `M${-meia},0V${r - altura}A${r},${r} 0 0 1 ${r - meia},${-altura}H${meia - r}A${r},${r} 0 0 1 ${meia},${r - altura}V0Z`;
}

/**
 * Gráfico de colunas de uma série (ex.: evolução mensal). SVG sem atributos `style`
 * (compatível com a CSP), cores pelos tokens do tema e dica ao passar o mouse ou focar.
 * Os valores ficam acessíveis sem a dica: rótulos diretos, rótulo de cada coluna e tabela.
 */
export default function GraficoColunas({ titulo, dados, colunas, formatarValor }: GraficoColunasProps) {
    const [ativo, setAtivo] = useState<number | null>(null);

    const total = dados.length;
    const { teto, marcas } = escalaLimpa(Math.max(...dados.map((ponto) => ponto.valor)));
    const alturaDe = (valor: number) => (valor / teto) * ALTURA;
    const centro = (indice: number) => ((indice + 0.5) * 100) / total;

    // Rótulos diretos só onde importam: o mês atual e o maior valor.
    const indiceMaximo = dados.reduce((maior, ponto, i) => (ponto.valor > dados[maior].valor ? i : maior), 0);
    const rotulados = new Set([total - 1, indiceMaximo]);

    const pontoAtivo = ativo === null ? null : dados[ativo];

    return (
        <figure className="grafico">
            <div className="grafico-colunas">
                <svg className="grafico-colunas__eixo" height={ALTURA_TOTAL} aria-hidden="true">
                    {marcas.map((marca) => (
                        <text key={marca} x="100%" y={TOPO + ALTURA - alturaDe(marca)} dy="0.32em" textAnchor="end">
                            {marca}
                        </text>
                    ))}
                </svg>

                <div className="grafico-colunas__area">
                    <svg width="100%" height={ALTURA_TOTAL} role="group" aria-label={titulo}>
                        {marcas.map((marca) => {
                            const y = TOPO + ALTURA - alturaDe(marca);

                            return (
                                <line
                                    key={marca}
                                    className={marca === 0 ? 'grafico__base' : 'grafico__grade'}
                                    x1="0"
                                    x2="100%"
                                    y1={y}
                                    y2={y}
                                />
                            );
                        })}

                        {dados.map((ponto, indice) => {
                            const altura = alturaDe(ponto.valor);

                            return (
                                <g key={ponto.chave}>
                                    {/* Alvo do mouse e do teclado: a faixa inteira do mês, maior que a coluna. */}
                                    <rect
                                        className="grafico__alvo"
                                        x={`${(indice * 100) / total}%`}
                                        y="0"
                                        width={`${100 / total}%`}
                                        height={ALTURA_TOTAL}
                                        tabIndex={0}
                                        role="img"
                                        aria-label={`${ponto.rotuloCompleto}: ${formatarValor(ponto.valor)}`}
                                        onPointerEnter={() => setAtivo(indice)}
                                        onPointerLeave={() => setAtivo(null)}
                                        onFocus={() => setAtivo(indice)}
                                        onBlur={() => setAtivo(null)}
                                    />
                                    <svg
                                        x={`${centro(indice)}%`}
                                        y={TOPO + ALTURA}
                                        overflow="visible"
                                        className="grafico__marca"
                                    >
                                        {altura > 0 && (
                                            <path
                                                className={cn(
                                                    'grafico__coluna',
                                                    ativo === indice && 'grafico__coluna--ativa',
                                                )}
                                                d={caminhoColuna(altura)}
                                            />
                                        )}
                                        {rotulados.has(indice) && (
                                            <text className="grafico__valor" y={-altura - 7} textAnchor="middle">
                                                {ponto.valor}
                                            </text>
                                        )}
                                        <text className="grafico__rotulo" y={BASE - 8} textAnchor="middle">
                                            {ponto.rotulo}
                                        </text>
                                    </svg>
                                </g>
                            );
                        })}
                    </svg>

                    {pontoAtivo && ativo !== null && (
                        // Posição definida no navegador (CSSOM), nunca no HTML do servidor.
                        <div
                            className={cn(
                                'grafico__dica',
                                ativo < total / 3 && 'grafico__dica--inicio',
                                ativo >= (total * 2) / 3 && 'grafico__dica--fim',
                            )}
                            style={{ left: `${centro(ativo)}%`, top: TOPO + ALTURA - alturaDe(pontoAtivo.valor) }}
                            aria-hidden="true"
                        >
                            <strong>{formatarValor(pontoAtivo.valor)}</strong>
                            <span>{pontoAtivo.rotuloCompleto}</span>
                        </div>
                    )}
                </div>
            </div>

            <TabelaGrafico
                titulo={titulo}
                colunas={colunas}
                linhas={dados.map((ponto) => ({
                    chave: ponto.chave,
                    rotulo: ponto.rotuloCompleto,
                    valor: ponto.valor,
                }))}
            />
        </figure>
    );
}
