import { alterarStatus } from '@/lib/acoes';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda } from '@/types';

/** Altera o status da demanda assim que uma opção é escolhida (RF07). */
export default function SeletorStatus({ demanda, url }: { demanda: Demanda; url: string }) {
    const { referencias } = usePropsPagina();

    return (
        <div className="status-form">
            <select
                aria-label={`Status de ${demanda.titulo}`}
                value={demanda.status}
                onChange={(evento) => alterarStatus(url, evento.target.value)}
            >
                {Object.entries(referencias.status).map(([valor, rotulo]) => (
                    <option key={valor} value={valor}>
                        {rotulo}
                    </option>
                ))}
            </select>
        </div>
    );
}
