import { cn } from '@/lib/classes';

/** Bloco de carregamento (skeleton). Decorativo: o estado é anunciado por aria-busy no contêiner. */
export function Bloco({ className }: { className?: string }) {
    return <span aria-hidden="true" className={cn('esqueleto block', className)} />;
}

/** Cartões de demanda em carregamento (mesma grade do feed). */
export function EsqueletoCartoes({ quantidade = 6 }: { quantidade?: number }) {
    return (
        <div className="mx-auto grid max-w-[1240px] grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3" aria-hidden="true">
            {Array.from({ length: quantidade }, (_, indice) => (
                <div key={indice} className="demand-card gap-3 p-5">
                    <div className="flex items-center justify-between">
                        <Bloco className="size-[42px] rounded-xl" />
                        <Bloco className="h-6 w-28 rounded-full" />
                    </div>
                    <Bloco className="mt-3 h-3 w-24" />
                    <Bloco className="h-5 w-3/4" />
                    <Bloco className="h-3 w-1/3" />
                    <Bloco className="h-14 w-full" />
                    <Bloco className="mt-2 h-9 w-full rounded-xl" />
                </div>
            ))}
        </div>
    );
}

/** Linhas de tabela em carregamento. */
export function EsqueletoLinhas({ linhas = 5, colunas }: { linhas?: number; colunas: number }) {
    return (
        <>
            {Array.from({ length: linhas }, (_, linha) => (
                <tr key={linha} aria-hidden="true">
                    {Array.from({ length: colunas }, (_, coluna) => (
                        <td key={coluna}>
                            <Bloco className={cn('h-4', coluna === 0 ? 'w-40' : 'w-20')} />
                        </td>
                    ))}
                </tr>
            ))}
        </>
    );
}
