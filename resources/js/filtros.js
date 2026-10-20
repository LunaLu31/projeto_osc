/**
 * Envia o formulário assim que um campo marcado com [data-auto-submit] muda
 * (filtros do feed e status das demandas nos painéis).
 */
export function iniciarAutoEnvio() {
    document.querySelectorAll('[data-auto-submit]').forEach((campo) => {
        campo.addEventListener('change', () => campo.form?.requestSubmit());
    });
}
