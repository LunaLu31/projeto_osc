import type { ReactNode } from 'react';
import Icone, { type NomeIcone } from './Icone';

interface MetricaCardProps {
    icone: NomeIcone;
    cor: 'blue' | 'green' | 'orange';
    valor: number;
    rotulo: string;
    detalhe?: ReactNode;
}

export default function MetricaCard({ icone, cor, valor, rotulo, detalhe }: MetricaCardProps) {
    return (
        <article>
            <span className={`metric-icon metric-icon--${cor}`}>
                <Icone nome={icone} />
            </span>
            <div>
                <strong>{valor}</strong>
                <span>{rotulo}</span>
                {detalhe && <small>{detalhe}</small>}
            </div>
        </article>
    );
}
