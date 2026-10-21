import type { ReactNode } from 'react';
import { cn } from '@/lib/classes';

/** Bloco de conteúdo dos painéis (listas, tabelas e formulários). */
export default function CartaoPainel({ className, children }: { className?: string; children: ReactNode }) {
    return <section className={cn('dashboard-list px-[15px] py-5 sm:p-7', className)}>{children}</section>;
}
