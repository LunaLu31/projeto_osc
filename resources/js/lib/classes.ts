/** Junta classes CSS, ignorando valores vazios (uso: cn('a', condicao && 'b')). */
export function cn(...classes: (string | false | null | undefined)[]): string {
    return classes.filter(Boolean).join(' ');
}

/** Grade de campos: uma coluna no celular, duas a partir de 561px. */
export const GRADE_FORMULARIO = 'grid grid-cols-1 gap-x-3.5 sm:grid-cols-2';

/** Cartão de formulário com respiro menor no celular. */
export const CARTAO_FORMULARIO = 'form-card px-5 py-[26px] sm:p-[34px]';
