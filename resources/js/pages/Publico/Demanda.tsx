import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import AcoesFormulario from '@/components/AcoesFormulario';
import Campo from '@/components/Campo';
import Container from '@/components/Container';
import Icone from '@/components/Icone';
import StatusBadge from '@/components/StatusBadge';
import Turnstile from '@/components/Turnstile';
import LayoutSite from '@/layouts/LayoutSite';
import { GRADE_FORMULARIO } from '@/lib/classes';
import { formatarData, resumir } from '@/lib/formatar';
import { usePropsPagina } from '@/lib/pagina';
import type { Demanda as DemandaTipo, TipoInteresse } from '@/types';

interface DemandaProps {
    demanda: DemandaTipo;
    antiSpam: { carregadoEm: string; turnstileSiteKey: string | null };
}

/** Detalhe público da demanda + formulário "Quero ajudar" (RF05), sem login. */
export default function Demanda({ demanda, antiSpam }: DemandaProps) {
    const { referencias } = usePropsPagina();
    const osc = demanda.osc!;

    const formulario = useForm({
        nome_apoiador: '',
        tipo_interesse: '' as TipoInteresse | '',
        telefone: '',
        email: '',
        mensagem: '',
        consentimento: false,
        // Anti-spam: honeypot (deve ficar vazio), instante de carregamento e token do Turnstile.
        website: '',
        _carregado_em: antiSpam.carregadoEm,
        'cf-turnstile-response': '',
    });
    const { data, setData, errors, processing } = formulario;

    const enviar = (evento: FormEvent) => {
        evento.preventDefault();
        formulario.post(route('demandas.interesse', demanda.id), { preserveScroll: true });
    };

    const errosAntiSpam = [errors.website, errors._carregado_em, errors['cf-turnstile-response']].filter(Boolean);

    return (
        <LayoutSite titulo={demanda.titulo} descricao={resumir(`${osc.nome} precisa de ajuda: ${demanda.descricao}`)}>
            <Container className="pt-12 pb-24">
                <Link href={`${route('feed')}#demandas`} className="back-link">
                    <Icone nome="back" tamanho={16} /> Voltar para as demandas
                </Link>

                <article className="detail-card">
                    <div className="grid grid-cols-1 md:grid-cols-[0.75fr_1.25fr]">
                        <aside className="detail-aside px-7 py-[30px] md:px-[38px] md:py-[46px]">
                            <span className="eyebrow">Você está ajudando</span>
                            <div className="org-avatar org-avatar--large hidden md:grid" aria-hidden="true">
                                {osc.iniciais}
                            </div>
                            <h2>{osc.nome}</h2>
                            <span className="detail-aside__area">{osc.area_atuacao}</span>
                            {osc.descricao && <p>{osc.descricao}</p>}
                            <div className="verified mt-4 md:mt-[30px]">
                                <Icone nome="shield" tamanho={18} />
                                Organização cadastrada pela CONECTA_OSC
                            </div>
                        </aside>

                        <section className="detail-content px-[22px] py-7 sm:p-[46px]">
                            <div className="badges">
                                {demanda.urgente && <span className="badge badge--urgent">Urgente</span>}
                                <StatusBadge status={demanda.status} />
                            </div>
                            <span className="eyebrow">{demanda.categoria}</span>
                            <h1>{demanda.titulo}</h1>
                            <p className="detail-description">{demanda.descricao}</p>
                            <div className="detail-grid mt-6 grid grid-cols-1 gap-2 sm:grid-cols-3">
                                <div className="grid grid-cols-[26px_1fr] gap-x-1 sm:flex sm:flex-col">
                                    <Icone nome="box" tamanho={18} className="row-span-2 mt-0.5 sm:mt-0 sm:mb-3" />
                                    <span>Quantidade</span>
                                    <strong>{demanda.quantidade} un.</strong>
                                </div>
                                <div className="grid grid-cols-[26px_1fr] gap-x-1 sm:flex sm:flex-col">
                                    <Icone nome="calendar" tamanho={18} className="row-span-2 mt-0.5 sm:mt-0 sm:mb-3" />
                                    <span>Prazo</span>
                                    <strong>{formatarData(demanda.prazo) ?? 'Sem prazo'}</strong>
                                </div>
                                <div className="grid grid-cols-[26px_1fr] gap-x-1 sm:flex sm:flex-col">
                                    <Icone nome="location" tamanho={18} className="row-span-2 mt-0.5 sm:mt-0 sm:mb-3" />
                                    <span>Localização</span>
                                    <strong>{osc.endereco || 'Não informado'}</strong>
                                </div>
                            </div>
                        </section>
                    </div>

                    <form
                        id="quero-ajudar"
                        className="interest-form px-[22px] py-7 sm:px-[46px] sm:pt-[38px] sm:pb-[46px]"
                        onSubmit={enviar}
                    >
                        <span className="eyebrow">Falta pouco</span>
                        <h2>Como você quer ajudar?</h2>
                        <p>
                            Não é necessário criar uma conta. A <strong>{osc.nome}</strong> receberá seu contato.
                        </p>

                        {Object.keys(errors).length > 0 && (
                            <div className="alert alert--error mt-5" role="alert">
                                <Icone nome="warning" />
                                <span>Revise os campos destacados para enviar seu interesse.</span>
                            </div>
                        )}

                        <div className={GRADE_FORMULARIO}>
                            <Campo rotulo="Seu nome" erro={errors.nome_apoiador}>
                                <input
                                    type="text"
                                    value={data.nome_apoiador}
                                    onChange={(e) => setData('nome_apoiador', e.target.value)}
                                    placeholder="Nome completo"
                                    maxLength={100}
                                    autoComplete="name"
                                    required
                                />
                            </Campo>
                            <Campo rotulo="Tipo de contribuição" erro={errors.tipo_interesse}>
                                <select
                                    value={data.tipo_interesse}
                                    onChange={(e) => setData('tipo_interesse', e.target.value as TipoInteresse)}
                                    required
                                >
                                    <option value="" disabled>
                                        Selecione uma opção
                                    </option>
                                    {referencias.tiposInteresse.map((tipo) => (
                                        <option key={tipo} value={tipo}>
                                            {tipo}
                                        </option>
                                    ))}
                                </select>
                            </Campo>
                            <Campo rotulo="Telefone" erro={errors.telefone} dica="Informe telefone, e-mail ou ambos.">
                                <input
                                    type="tel"
                                    value={data.telefone}
                                    onChange={(e) => setData('telefone', e.target.value)}
                                    placeholder="(71) 99999-9999"
                                    maxLength={20}
                                    autoComplete="tel"
                                />
                            </Campo>
                            <Campo rotulo="E-mail" erro={errors.email}>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="voce@email.com"
                                    maxLength={150}
                                    autoComplete="email"
                                />
                            </Campo>
                        </div>
                        <Campo rotulo="Mensagem" erro={errors.mensagem} opcional>
                            <textarea
                                rows={4}
                                value={data.mensagem}
                                onChange={(e) => setData('mensagem', e.target.value)}
                                maxLength={2000}
                                placeholder="Conte brevemente como você pode contribuir..."
                            />
                        </Campo>

                        <div className="privacy-note">
                            <Icone nome="shield" />
                            Seus dados serão enviados somente para esta organização e não ficam visíveis publicamente.
                        </div>

                        <label className="checkbox-field">
                            <input
                                type="checkbox"
                                checked={data.consentimento}
                                onChange={(e) => setData('consentimento', e.target.checked)}
                                required
                            />
                            <span>
                                Concordo em compartilhar meus dados com esta OSC para retorno sobre esta demanda.
                            </span>
                        </label>
                        {errors.consentimento && <span className="field__error">{errors.consentimento}</span>}

                        {/* Honeypot: invisível para pessoas; robôs costumam preenchê-lo. */}
                        <div className="campo-armadilha" aria-hidden="true">
                            <label>
                                Deixe este campo em branco
                                <input
                                    type="text"
                                    tabIndex={-1}
                                    autoComplete="off"
                                    value={data.website}
                                    onChange={(e) => setData('website', e.target.value)}
                                />
                            </label>
                        </div>

                        {antiSpam.turnstileSiteKey && (
                            <div className="turnstile">
                                <Turnstile
                                    siteKey={antiSpam.turnstileSiteKey}
                                    onToken={(token) => setData('cf-turnstile-response', token)}
                                />
                            </div>
                        )}
                        {errosAntiSpam.map((erro) => (
                            <span key={erro} className="field__error">
                                {erro}
                            </span>
                        ))}

                        <AcoesFormulario>
                            <button type="submit" className="button button--primary" disabled={processing}>
                                {processing ? 'Enviando…' : 'Enviar interesse'} <Icone nome="arrow" />
                            </button>
                        </AcoesFormulario>
                    </form>
                </article>
            </Container>
        </LayoutSite>
    );
}
