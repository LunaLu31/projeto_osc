import ChamadaDemandas from '@/components/ChamadaDemandas';
import Icone from '@/components/Icone';
import LayoutSite from '@/layouts/LayoutSite';

const diferenciais = [
    'Demandas organizadas e atualizadas',
    'Contatos protegidos pela LGPD',
    'Acompanhamento do status de cada causa',
];

/** Aba "Sobre a rede" do site: o que é a Redcam e por que ela existe. */
export default function Sobre() {
    return (
        <LayoutSite
            titulo="Sobre a rede"
            descricao="A Redcam aproxima organizações sociais, apoiadores e a UOSCC em um ambiente confiável em Camaçari."
        >
            <section className="section px-5 py-[78px] sm:px-[max(7vw,24px)] sm:py-28">
                <div className="about-section mx-auto grid max-w-[1280px] grid-cols-1 items-center gap-[45px] md:grid-cols-[1fr_0.8fr] md:gap-25">
                    <div className="about-section__copy">
                        <span className="eyebrow">Uma rede feita em Camaçari</span>
                        <h1>Informação centralizada. Impacto multiplicado.</h1>
                        <p>
                            A Redcam aproxima organizações sociais, apoiadores e a UOSCC em um ambiente confiável. Aqui,
                            necessidades reais não se perdem no fluxo das redes sociais.
                        </p>
                        <ul>
                            {diferenciais.map((item) => (
                                <li key={item}>
                                    <Icone nome="check" /> {item}
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div className="about-section__quote px-7 py-[38px] sm:p-[55px]">
                        <Icone nome="spark" tamanho={28} />
                        <blockquote>“Quando a informação chega à pessoa certa, a solidariedade vira ação.”</blockquote>
                        <p>Rede UOSCC · Camaçari</p>
                    </div>
                </div>
            </section>

            <ChamadaDemandas />
        </LayoutSite>
    );
}
