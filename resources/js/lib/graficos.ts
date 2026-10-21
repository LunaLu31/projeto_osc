/**
 * Utilitários dos gráficos do painel (components/graficos).
 */

const mesCurto = new Intl.DateTimeFormat('pt-BR', { month: 'short', timeZone: 'UTC' });
const mesLongo = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric', timeZone: 'UTC' });

/** "2026-05" → "mai." (ou "maio de 2026" com `completo`). Sem conversão de fuso: é um mês de calendário. */
export function formatarMes(mes: string, completo = false): string {
    const [ano, numero] = mes.split('-').map(Number);
    const data = new Date(Date.UTC(ano, numero - 1, 15));

    return (completo ? mesLongo : mesCurto).format(data);
}

/**
 * Escala do eixo com marcas em números redondos (passos de 1, 2 ou 5 × 10ⁿ, nunca fracionários).
 * O teto é a primeira marca que alcança o maior valor.
 */
export function escalaLimpa(maximo: number, divisoes = 4): { teto: number; marcas: number[] } {
    if (maximo <= 0) {
        return { teto: 1, marcas: [0, 1] };
    }

    const bruto = maximo / divisoes;
    const magnitude = 10 ** Math.floor(Math.log10(bruto));
    const passo = Math.max(1, [1, 2, 5, 10].map((fator) => fator * magnitude).find((p) => p >= bruto) ?? bruto);
    const teto = Math.ceil(maximo / passo) * passo;

    return { teto, marcas: Array.from({ length: teto / passo + 1 }, (_, i) => i * passo) };
}
