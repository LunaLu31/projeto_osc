/**
 * Menu "hambúrguer" do cabeçalho público.
 */
export function iniciarMenu() {
    document.querySelectorAll('[data-menu-toggle]').forEach((botao) => {
        const menu = document.getElementById(botao.getAttribute('aria-controls'));

        if (!menu) {
            return;
        }

        const alternar = (aberto) => {
            botao.setAttribute('aria-expanded', String(aberto));
            botao.setAttribute('aria-label', aberto ? 'Fechar menu de navegação' : 'Abrir menu de navegação');
            menu.classList.toggle('nav--open', aberto);
        };

        botao.addEventListener('click', () => alternar(botao.getAttribute('aria-expanded') !== 'true'));
        menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => alternar(false)));

        document.addEventListener('click', (evento) => {
            if (!botao.contains(evento.target) && !menu.contains(evento.target)) {
                alternar(false);
            }
        });

        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape' && botao.getAttribute('aria-expanded') === 'true') {
                alternar(false);
                botao.focus();
            }
        });
    });
}
