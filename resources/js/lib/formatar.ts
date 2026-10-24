/**
 * Formatação de datas e textos para exibição em pt-BR.
 * O fuso é fixo (o da aplicação) para que o HTML gerado no servidor (SSR)
 * e o renderizado no navegador sejam idênticos.
 */

export const FUSO_HORARIO = 'America/Bahia';

const dataHora = new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short', timeZone: FUSO_HORARIO });
const diaMesHora = new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: FUSO_HORARIO,
});
const dataIso = new Intl.DateTimeFormat('en-CA', { timeZone: FUSO_HORARIO });
const dataCurta = new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeZone: FUSO_HORARIO });

/** "2026-11-01" → "01/11/2026" (sem conversão de fuso: é uma data de calendário). */
export function formatarData(data: string | null | undefined): string | null {
    if (!data) {
        return null;
    }

    const [ano, mes, dia] = data.slice(0, 10).split('-');

    return `${dia}/${mes}/${ano}`;
}

/** Data e hora ISO 8601 → "20/10/2026 17:52" no fuso da aplicação. */
export function formatarDataHora(iso: string | null | undefined, curta = false): string {
    if (!iso) {
        return '—';
    }

    return (curta ? diaMesHora : dataHora).format(new Date(iso)).replace(',', '');
}

/** Instante ISO 8601 → "20/10/2026" (dia no fuso da aplicação). */
export function formatarDiaDoInstante(iso: string | null | undefined): string {
    return iso ? dataCurta.format(new Date(iso)) : '—';
}

/** Data de hoje (AAAA-MM-DD) no fuso da aplicação. */
export function hojeIso(): string {
    return dataIso.format(new Date());
}

export function plural(quantidade: number, singular: string, pluralTexto: string): string {
    return `${quantidade} ${quantidade === 1 ? singular : pluralTexto}`;
}

/** Texto curto para meta tags (descrição de páginas). */
export function resumir(texto: string, limite = 160): string {
    const limpo = texto.replace(/\s+/g, ' ').trim();

    return limpo.length > limite ? `${limpo.slice(0, limite - 1).trimEnd()}…` : limpo;
}

/** Remove caracteres não numéricos para links tel:. */
export function apenasTelefone(telefone: string): string {
    return telefone.replace(/[^0-9+]/g, '');
}
