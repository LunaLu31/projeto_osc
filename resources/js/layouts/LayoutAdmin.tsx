import type { ReactNode } from 'react';
import { route } from 'ziggy-js';
import Alertas from '@/components/Alertas';
import { usePropsPagina } from '@/lib/pagina';
import LayoutPainel from './LayoutPainel';

/** Layout do portal administrativo. */
export default function LayoutAdmin({ titulo, children }: { titulo: string; children: ReactNode }) {
    const { auth } = usePropsPagina();

    if (!auth.admin) {
        return null;
    }

    return (
        <LayoutPainel
            titulo={titulo}
            usuario={auth.admin}
            perfil="Administrador UOSCC"
            inicio={route('admin.painel')}
            sair={route('admin.logout')}
        >
            <Alertas />
            {children}
        </LayoutPainel>
    );
}
