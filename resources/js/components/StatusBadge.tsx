import { usePropsPagina } from '@/lib/pagina';
import type { StatusDemanda } from '@/types';

const modificadores: Record<StatusDemanda, string> = {
    Aberta: 'badge--aberta',
    Parcial: 'badge--parcial',
    Concluída: 'badge--concluida',
};

export default function StatusBadge({ status }: { status: StatusDemanda }) {
    const { referencias } = usePropsPagina();

    return <span className={`badge ${modificadores[status]}`}>{referencias.status[status]}</span>;
}
