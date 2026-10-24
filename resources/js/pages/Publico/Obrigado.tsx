import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import Container from '@/components/Container';
import Icone from '@/components/Icone';
import LayoutSite from '@/layouts/LayoutSite';

/** Tela de sucesso após manifestar interesse (RF06). */
export default function Obrigado({ organizacao }: { organizacao: string }) {
    return (
        <LayoutSite titulo="Interesse enviado">
            <Container className="pt-12 pb-24">
                <div className="success-state px-[22px] py-[34px] sm:px-10 sm:py-[46px]" role="status">
                    <div className="success-state__icon">
                        <Icone nome="check" tamanho={32} />
                    </div>
                    <span className="eyebrow">Interesse enviado</span>
                    <h1>Obrigado por fazer parte dessa rede!</h1>
                    <p>
                        Seus dados foram compartilhados com segurança apenas com a <strong>{organizacao}</strong>. A
                        organização entrará em contato com você.
                    </p>
                    <div className="privacy-note">
                        <Icone nome="shield" />
                        Seus dados de contato não ficam visíveis publicamente.
                    </div>
                    <Link href={`${route('feed')}#demandas`} className="button button--primary button--block">
                        Voltar para as demandas
                    </Link>
                </div>
            </Container>
        </LayoutSite>
    );
}
