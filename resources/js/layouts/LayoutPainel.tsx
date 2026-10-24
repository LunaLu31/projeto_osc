import { Head, Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import AlternarTema from '@/components/AlternarTema';
import Container from '@/components/Container';
import Logo from '@/components/Logo';
import MenuPainel, { type SecaoPainel } from '@/components/MenuPainel';

interface LayoutPainelProps {
    titulo: string;
    usuario: { nome: string; iniciais: string };
    perfil: string;
    inicio: string;
    sair: string;
    /** Abas do menu lateral, sempre à vista: com os nomes no desktop, só os ícones em telas pequenas. */
    secoes: SecaoPainel[];
    children: ReactNode;
}

/** Layout dos painéis privados (OSC e administrador): cabeçalho e menu lateral fixo. */
export default function LayoutPainel({ titulo, usuario, perfil, inicio, sair, secoes, children }: LayoutPainelProps) {
    return (
        <>
            <Head title={titulo}>
                <meta name="robots" content="noindex" />
            </Head>
            <a href="#conteudo" className="skip-link">
                Pular para o conteúdo
            </a>

            <header className="dashboard-header sticky top-0 z-40 flex min-h-[78px] items-center justify-between gap-4 px-[max(5vw,24px)]">
                <Logo href={inicio} subtitulo={perfil} compactoNoCelular />
                <div className="dashboard-user">
                    <AlternarTema />
                    <div className="org-avatar" aria-hidden="true">
                        {usuario.iniciais}
                    </div>
                    <span className="dashboard-user__info hidden sm:flex">
                        <strong>{usuario.nome}</strong>
                        <small>{perfil}</small>
                    </span>
                    <Link href={sair} method="post" as="button" className="button button--ghost button--small">
                        Sair
                    </Link>
                </div>
            </header>

            <div className="flex">
                <aside className="menu-lateral">
                    <MenuPainel secoes={secoes} />
                </aside>

                <div className="min-w-0 flex-1">
                    <Container as="main" id="conteudo" className="pt-14 pb-25">
                        {children}
                    </Container>
                </div>
            </div>
        </>
    );
}
