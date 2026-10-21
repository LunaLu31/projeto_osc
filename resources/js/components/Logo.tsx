import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { cn } from '@/lib/classes';
import marca from '../../images/redcam-marca.png';

interface LogoProps {
    href?: string;
    subtitulo?: string;
    /** Esconde o nome no celular (cabeçalho dos painéis, onde falta espaço). */
    compactoNoCelular?: boolean;
}

export default function Logo({ href, subtitulo = 'Rede que transforma', compactoNoCelular = false }: LogoProps) {
    return (
        // O nome acessível é o próprio texto visível (no celular compacto, ele fica só para leitores de tela).
        <Link href={href ?? route('feed')} className="logo">
            <span className="logo__mark">
                {/* Decorativa: o nome "Redcam" ao lado já identifica o link. */}
                <img src={marca} alt="" width={42} height={42} />
            </span>
            <span className={cn('logo__copy', compactoNoCelular && 'sr-only sm:not-sr-only')}>
                <strong>Redcam</strong>
                <small>{subtitulo}</small>
            </span>
        </Link>
    );
}
