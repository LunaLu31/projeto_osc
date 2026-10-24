interface TabelaGraficoProps {
    titulo: string;
    colunas: [string, string];
    linhas: { chave: string; rotulo: string; valor: number }[];
}

/** Equivalente em tabela de um gráfico (acessível sem mouse, sem cor e para leitores de tela). */
export default function TabelaGrafico({ titulo, colunas, linhas }: TabelaGraficoProps) {
    return (
        <details className="grafico__tabela">
            <summary>Ver dados em tabela</summary>
            <table>
                <caption className="sr-only">{titulo}</caption>
                <thead>
                    <tr>
                        <th scope="col">{colunas[0]}</th>
                        <th scope="col">{colunas[1]}</th>
                    </tr>
                </thead>
                <tbody>
                    {linhas.map((linha) => (
                        <tr key={linha.chave}>
                            <th scope="row">{linha.rotulo}</th>
                            <td>{linha.valor}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </details>
    );
}
