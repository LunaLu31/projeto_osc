/**
 * - [data-confirm]: pede confirmação antes de ações destrutivas.
 * - Desabilita o botão de envio para evitar cadastros duplicados por duplo clique.
 */
export function iniciarFormularios() {
    document.addEventListener('submit', (evento) => {
        const formulario = evento.target;
        const mensagem = formulario.dataset.confirm;

        if (mensagem && !window.confirm(mensagem)) {
            evento.preventDefault();
            return;
        }

        // Formulários de filtro (GET) podem ser reenviados à vontade.
        if (formulario.method.toLowerCase() === 'get') {
            return;
        }

        formulario.querySelectorAll('button[type="submit"]').forEach((botao) => {
            botao.disabled = true;
        });
    });

    // Ao voltar pelo histórico (bfcache), reabilita os botões.
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('button[type="submit"]:disabled').forEach((botao) => {
            botao.disabled = false;
        });
    });
}
