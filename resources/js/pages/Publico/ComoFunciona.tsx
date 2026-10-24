import ChamadaDemandas from '@/components/ChamadaDemandas';
import Icone from '@/components/Icone';
import LayoutSite from '@/layouts/LayoutSite';

const passos = [
    {
        icone: 'search',
        titulo: 'Encontre uma causa',
        texto: 'Use os filtros para descobrir necessidades que combinam com a forma como você quer ajudar.',
    },
    {
        icone: 'heart',
        titulo: 'Manifeste seu interesse',
        texto: 'Preencha um formulário rápido. Não precisa criar uma conta nem expor seus dados publicamente.',
    },
    {
        icone: 'people',
        titulo: 'Conecte-se com a OSC',
        texto: 'A organização recebe seu contato e combina diretamente os próximos passos com você.',
    },
] as const;

/** Aba "Como funciona" do site: os três passos para ajudar uma organização. */
export default function ComoFunciona() {
    return (
        <LayoutSite
            titulo="Como funciona"
            descricao="Em três passos você encontra uma causa em Camaçari, manifesta interesse e se conecta com a organização."
        >
            <section className="section section--tinted px-5 py-[78px] sm:px-[max(7vw,24px)] sm:py-28">
                <div className="section-heading mx-auto mb-[38px] flex max-w-[1240px] flex-col items-start gap-5 text-center md:flex-row md:items-end md:justify-center">
                    <div>
                        <span className="eyebrow">Simples e transparente</span>
                        <h1>Ajudar ficou mais fácil</h1>
                        <p>Em três passos, sua vontade se transforma em impacto real.</p>
                    </div>
                </div>
                <div className="steps mx-auto grid max-w-[1100px] grid-cols-1 gap-[42px] md:grid-cols-3 md:gap-[52px]">
                    {passos.map((passo, indice) => (
                        <article key={passo.titulo}>
                            <span className="step-number">{String(indice + 1).padStart(2, '0')}</span>
                            <div className="step-icon">
                                <Icone nome={passo.icone} tamanho={29} />
                            </div>
                            <h2>{passo.titulo}</h2>
                            <p className="mx-auto max-w-[420px] md:mx-0 md:max-w-none">{passo.texto}</p>
                        </article>
                    ))}
                </div>
            </section>

            <ChamadaDemandas />
        </LayoutSite>
    );
}
