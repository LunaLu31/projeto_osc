import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Icone from './Icone';

/** Chamada final das páginas institucionais: leva o visitante de volta às demandas do feed. */
export default function ChamadaDemandas() {
    return (
        <section className="cta-section px-[22px] py-14 sm:px-6 sm:py-[76px]">
            <span className="eyebrow">Faça parte desta rede</span>
            <h2>Existe uma causa esperando por você.</h2>
            <p>
                Doe materiais, compartilhe conhecimento ou ofereça seu tempo. Toda contribuição começa com uma conexão.
            </p>
            <Link href={`${route('feed')}#demandas`} className="button button--light">
                Ver demandas abertas <Icone nome="arrow" />
            </Link>
        </section>
    );
}
