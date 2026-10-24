import type { ReactNode } from 'react';
import { cn } from '@/lib/classes';

/** Largura padrão das páginas internas: margem de 16px no celular, até 1180px no desktop. */
export default function Container({
    className,
    children,
    as: Elemento = 'div',
    id,
}: {
    className?: string;
    children: ReactNode;
    as?: 'div' | 'main';
    id?: string;
}) {
    return (
        <Elemento id={id} className={cn('mx-auto w-[calc(100%-32px)] sm:w-[min(1180px,calc(100%-48px))]', className)}>
            {children}
        </Elemento>
    );
}
