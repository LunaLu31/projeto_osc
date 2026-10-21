import Icone from './Icone';

const UM_ANO = 60 * 60 * 24 * 365;

/** Tema em uso agora: o escolhido (data-theme) ou, sem escolha salva, o do dispositivo. */
function temaEscuroAtivo(): boolean {
    const escolhido = document.documentElement.dataset.theme;

    if (escolhido === 'claro' || escolhido === 'escuro') {
        return escolhido === 'escuro';
    }

    return window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
}

/**
 * Alterna entre os temas claro e escuro e lembra a escolha no cookie `tema`.
 * Ícone e rótulo seguem a variante `dark:` do CSS, então o HTML do servidor
 * já sai certo mesmo antes de existir uma escolha (quando vale o tema do dispositivo).
 */
export default function AlternarTema() {
    const alternar = () => {
        const proximo = temaEscuroAtivo() ? 'claro' : 'escuro';
        document.documentElement.dataset.theme = proximo;
        document.cookie = `tema=${proximo}; path=/; max-age=${UM_ANO}; samesite=lax`;
    };

    return (
        <button type="button" className="menu-button" onClick={alternar} title="Alternar tema claro/escuro">
            <span className="grid place-items-center dark:hidden">
                <Icone nome="sun" />
                <span className="sr-only">Tema claro. Mudar para tema escuro</span>
            </span>
            <span className="hidden place-items-center dark:grid">
                <Icone nome="moon" />
                <span className="sr-only">Tema escuro. Mudar para tema claro</span>
            </span>
        </button>
    );
}
