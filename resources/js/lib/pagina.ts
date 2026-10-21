import { usePage } from '@inertiajs/react';
import type { PropsCompartilhadas } from '@/types';

/** usePage() tipado com as props compartilhadas + as props específicas da página. */
export function usePropsPagina<T extends object = object>() {
    return usePage<PropsCompartilhadas & T>().props;
}
