import { router } from '@inertiajs/react';

/** Atualiza o status de uma demanda sem recarregar a página (RF07). */
export function alterarStatus(url: string, status: string): void {
    router.patch(url, { status }, { preserveScroll: true });
}
