import type { ReactNode } from 'react';

/**
 * Botões do rodapé de formulários: no celular ocupam a largura toda, com a ação
 * principal no topo (column-reverse); no desktop ficam alinhados à direita.
 */
export default function AcoesFormulario({ children }: { children: ReactNode }) {
    return (
        <div className="mt-[26px] flex flex-col-reverse gap-2.5 *:w-full sm:flex-row sm:justify-end sm:*:w-auto">
            {children}
        </div>
    );
}
