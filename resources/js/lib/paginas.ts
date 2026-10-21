import type { ResolvedComponent } from '@inertiajs/react';

export const NOME_APP = import.meta.env.VITE_APP_NAME || 'Redcam';

// Cada página vira um chunk separado, carregado sob demanda (mesma resolução no cliente e no SSR).
const paginas = import.meta.glob<{ default: ResolvedComponent }>('../pages/**/*.tsx');

export async function resolverPagina(nome: string): Promise<ResolvedComponent> {
    const pagina = paginas[`../pages/${nome}.tsx`];

    if (!pagina) {
        throw new Error(`Página Inertia não encontrada: ${nome}`);
    }

    return (await pagina()).default;
}

export function tituloDaPagina(titulo: string): string {
    return titulo ? `${titulo} | ${NOME_APP}` : `${NOME_APP} | Rede que transforma`;
}
