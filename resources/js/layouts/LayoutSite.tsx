import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useRef, useState, type ReactNode } from 'react';
import { route } from 'ziggy-js';
import AlternarTema from '@/components/AlternarTema';
import Icone from '@/components/Icone';
import Logo from '@/components/Logo';

const DESCRICAO_PADRAO =
    'Encontre necessidades reais de organizações sociais de Camaçari e ajude com doações, voluntariado ou conhecimento.';

interface LayoutSiteProps {
    titulo?: string;
    /** Descrição da página para buscadores e prévias de links compartilhados. */
    descricao?: string;
    children: ReactNode;
}

/** Layout da área pública (apoiadores). */
export default function LayoutSite({ titulo, descricao = DESCRICAO_PADRAO, children }: LayoutSiteProps) {
    const [menuAberto, setMenuAberto] = useState(false);
    const menu = useRef<HTMLDivElement>(null);
    const botaoMenu = useRef<HTMLButtonElement>(null);
    const feed = route('feed');

    useEffect(() => {
        if (!menuAberto) {
            return;
        }

        const fecharAoClicarFora = (evento: MouseEvent) => {
            if (!menu.current?.contains(evento.target as Node)) {
                setMenuAberto(false);
            }
        };
        const fecharComEsc = (evento: KeyboardEvent) => {
            if (evento.key === 'Escape') {
                setMenuAberto(false);
                botaoMenu.current?.focus();
            }
        };

        document.addEventListener('click', fecharAoClicarFora);
        document.addEventListener('keydown', fecharComEsc);

        return () => {
            document.removeEventListener('click', fecharAoClicarFora);
            document.removeEventListener('keydown', fecharComEsc);
        };
    }, [menuAberto]);

    // Fecha o menu a cada navegação.
    useEffect(() => router.on('navigate', () => setMenuAberto(false)), []);

    return (
        <>
            <Head title={titulo}>
                <meta head-key="description" name="description" content={descricao} />
                <meta head-key="og:type" property="og:type" content="website" />
                <meta head-key="og:site_name" property="og:site_name" content="Redcam" />
                <meta head-key="og:title" property="og:title" content={titulo ?? 'Redcam | Rede que transforma'} />
                <meta head-key="og:description" property="og:description" content={descricao} />
            </Head>
            <a href="#conteudo" className="skip-link">
                Pular para o conteúdo
            </a>

            <header className="site-header sticky top-0 z-40 flex min-h-[70px] items-center justify-between gap-4 px-[max(5vw,24px)] md:min-h-[82px]">
                <div className="navigation-menu" ref={menu}>
                    <button
                        ref={botaoMenu}
                        type="button"
                        className="menu-button"
                        aria-controls="menu-principal"
                        aria-expanded={menuAberto}
                        aria-label={menuAberto ? 'Fechar menu de navegação' : 'Abrir menu de navegação'}
                        onClick={() => setMenuAberto((aberto) => !aberto)}
                    >
                        <Icone nome={menuAberto ? 'close' : 'menu'} />
                    </button>
                    <nav
                        id="menu-principal"
                        className={menuAberto ? 'nav nav--open' : 'nav'}
                        aria-label="Navegação principal"
                    >
                        <a href={`${feed}#demandas`} onClick={() => setMenuAberto(false)}>
                            <Icone nome="home" /> Demandas
                        </a>
                        <a href={`${feed}#como-funciona`} onClick={() => setMenuAberto(false)}>
                            <Icone nome="spark" /> Como funciona
                        </a>
                        <a href={`${feed}#sobre`} onClick={() => setMenuAberto(false)}>
                            <Icone nome="people" /> Sobre a rede
                        </a>
                        <Link href={route('osc.login')}>
                            <Icone nome="user" /> Área da OSC
                        </Link>
                    </nav>
                </div>
                <div className="flex items-center gap-3">
                    <AlternarTema />
                    <Logo />
                </div>
            </header>

            <main id="conteudo">{children}</main>

            <footer>
                <div className="site-footer grid grid-cols-1 gap-9 px-[max(7vw,24px)] pt-[70px] pb-[50px] sm:grid-cols-2 sm:gap-15 md:grid-cols-[1.5fr_0.8fr_1fr]">
                    <div>
                        <Logo />
                        <p>Conectando quem quer ajudar com quem transforma Camaçari todos os dias.</p>
                    </div>
                    <div>
                        <strong>Navegue</strong>
                        <a href={`${feed}#demandas`}>Demandas</a>
                        <a href={`${feed}#como-funciona`}>Como funciona</a>
                        <Link href={route('osc.login')}>Área da OSC</Link>
                    </div>
                    <div className="footer-signature">
                        <strong>UOSCC / Redcam</strong>
                        <span>Um projeto pela comunidade de Camaçari.</span>
                    </div>
                </div>
                <div className="footer-bottom flex flex-col gap-1.5 sm:flex-row sm:justify-between">
                    <span>© {new Date().getFullYear()} Redcam. Todos os direitos reservados.</span>
                    <span>MVP · Versão A</span>
                </div>
            </footer>
        </>
    );
}
