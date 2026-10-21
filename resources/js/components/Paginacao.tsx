import { Link } from '@inertiajs/react';
import type { Paginado } from '@/types';

interface PaginacaoProps<T> {
    pagina: Paginado<T>;
    rotulo: string;
    /** Mantém a posição da página (use false quando os links levam a uma âncora). */
    manterRolagem?: boolean;
    /** Recarrega apenas estas props (útil com mais de uma lista paginada na página). */
    somente?: string[];
}

export default function Paginacao<T>({ pagina, rotulo, manterRolagem = true, somente }: PaginacaoProps<T>) {
    const { meta, links } = pagina;

    if (meta.last_page <= 1) {
        return null;
    }

    const botao = (href: string | null, texto: string, rel: string) =>
        href ? (
            <Link
                className="button button--secondary button--small"
                href={href}
                rel={rel}
                only={somente}
                preserveScroll={manterRolagem}
            >
                {texto}
            </Link>
        ) : (
            <span className="button button--secondary button--small" aria-disabled="true">
                {texto}
            </span>
        );

    return (
        <nav className="pagination" aria-label={rotulo}>
            {botao(links.prev, 'Anterior', 'prev')}
            <span>
                Página {meta.current_page} de {meta.last_page}
            </span>
            {botao(links.next, 'Próxima', 'next')}
        </nav>
    );
}
