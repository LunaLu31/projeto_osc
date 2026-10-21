import { Head } from '@inertiajs/react';
import type { ReactNode } from 'react';
import AlternarTema from '@/components/AlternarTema';
import Logo from '@/components/Logo';

/** Layout das telas de acesso (login da OSC/admin, configuração inicial e erros). */
export default function LayoutAutenticacao({
    titulo,
    subtitulo,
    children,
}: {
    titulo: string;
    subtitulo?: string;
    children: ReactNode;
}) {
    return (
        <>
            <Head title={titulo} />
            <main id="conteudo" className="auth-shell relative">
                <div className="absolute top-4 right-4">
                    <AlternarTema />
                </div>
                <div className="auth-card px-[22px] py-[30px] sm:p-[42px]">
                    <Logo subtitulo={subtitulo} />
                    {children}
                </div>
            </main>
        </>
    );
}
