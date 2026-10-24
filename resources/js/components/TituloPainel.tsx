import type { ReactNode } from 'react';

interface TituloPainelProps {
    rotulo: string;
    titulo: ReactNode;
    descricao: string;
    /** Ação principal da página (ex.: botão "Nova demanda"). */
    acao?: ReactNode;
}

/** Cabeçalho das páginas dos painéis: empilhado no celular, alinhado em linha no desktop. */
export default function TituloPainel({ rotulo, titulo, descricao, acao }: TituloPainelProps) {
    return (
        <section className="dashboard-title flex flex-col items-stretch gap-[22px] sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span className="eyebrow">{rotulo}</span>
                <h1>{titulo}</h1>
                <p>{descricao}</p>
            </div>
            {acao}
        </section>
    );
}
