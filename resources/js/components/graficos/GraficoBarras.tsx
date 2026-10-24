export interface ItemBarra {
    chave: string;
    rotulo: string;
    valor: number;
}

interface GraficoBarrasProps {
    titulo: string;
    itens: ItemBarra[];
    formatarValor: (valor: number) => string;
}

/** A maior barra ocupa no máximo esta fração da trilha, deixando espaço para o valor na ponta. */
const ALCANCE = 88;
const ESPESSURA = 16;
const RAIO = 4;

/**
 * Barras horizontais de uma série, na ordem recebida (ex.: status, tipos de interesse).
 * Cada barra traz o valor na ponta, então a lista em si já é o equivalente textual do gráfico.
 */
export default function GraficoBarras({ titulo, itens, formatarValor }: GraficoBarrasProps) {
    const maximo = Math.max(1, ...itens.map((item) => item.valor));

    return (
        <ul className="grafico grafico-barras" aria-label={titulo}>
            {itens.map((item) => {
                const largura = (item.valor / maximo) * ALCANCE;

                return (
                    <li key={item.chave}>
                        <span>
                            {item.rotulo}
                            <span className="sr-only">: {formatarValor(item.valor)}</span>
                        </span>
                        <svg width="100%" height={ESPESSURA + 8} aria-hidden="true">
                            {item.valor > 0 && (
                                <>
                                    <rect
                                        className="grafico__coluna"
                                        y="4"
                                        width={`${largura}%`}
                                        height={ESPESSURA}
                                        rx={RAIO}
                                    />
                                    {/* Cantos retos na linha de base: só a ponta é arredondada. */}
                                    <rect className="grafico__coluna" y="4" width={RAIO} height={ESPESSURA} />
                                </>
                            )}
                            <text
                                className="grafico__valor"
                                x={`${largura}%`}
                                dx={item.valor > 0 ? 8 : 0}
                                y="12"
                                dy="0.35em"
                            >
                                {item.valor}
                            </text>
                        </svg>
                    </li>
                );
            })}
        </ul>
    );
}
