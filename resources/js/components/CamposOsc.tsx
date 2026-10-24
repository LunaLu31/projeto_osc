import type { InertiaFormProps } from '@inertiajs/react';
import { GRADE_FORMULARIO } from '@/lib/classes';
import Campo from './Campo';

export interface DadosOsc {
    nome: string;
    area_atuacao: string;
    telefone: string;
    endereco: string;
    descricao: string;
    email: string;
    password: string;
    password_confirmation: string;
}

/** Campos da OSC compartilhados pelo cadastro e pela edição (RF02). */
export default function CamposOsc({
    formulario,
    editando,
}: {
    formulario: InertiaFormProps<DadosOsc>;
    editando: boolean;
}) {
    const { data, setData, errors } = formulario;

    return (
        <>
            <Campo rotulo="Nome da organização" erro={errors.nome}>
                <input value={data.nome} onChange={(e) => setData('nome', e.target.value)} maxLength={150} required />
            </Campo>
            <div className={GRADE_FORMULARIO}>
                <Campo rotulo="Área de atuação" erro={errors.area_atuacao}>
                    <input
                        value={data.area_atuacao}
                        onChange={(e) => setData('area_atuacao', e.target.value)}
                        maxLength={100}
                        placeholder="Ex.: Educação"
                        required
                    />
                </Campo>
                <Campo rotulo="Telefone" erro={errors.telefone}>
                    <input
                        type="tel"
                        value={data.telefone}
                        onChange={(e) => setData('telefone', e.target.value)}
                        maxLength={20}
                        placeholder="(71) 99999-9999"
                        required
                    />
                </Campo>
            </div>
            <Campo rotulo="Endereço" erro={errors.endereco}>
                <input
                    value={data.endereco}
                    onChange={(e) => setData('endereco', e.target.value)}
                    maxLength={255}
                    placeholder="Bairro, Camaçari - BA"
                    required
                />
            </Campo>
            <Campo rotulo="Descrição" erro={errors.descricao}>
                <textarea
                    rows={3}
                    maxLength={5000}
                    value={data.descricao}
                    onChange={(e) => setData('descricao', e.target.value)}
                    required
                />
            </Campo>
            <Campo rotulo="E-mail de acesso da OSC" erro={errors.email}>
                <input
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    maxLength={150}
                    autoComplete="off"
                    required
                />
            </Campo>
            <div className={GRADE_FORMULARIO}>
                <Campo
                    rotulo={editando ? 'Nova senha' : 'Senha inicial'}
                    erro={errors.password}
                    opcional
                    dica={
                        editando
                            ? 'Deixe em branco para manter a atual.'
                            : 'Deixe em branco para gerar automaticamente.'
                    }
                >
                    <input
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        minLength={8}
                        autoComplete="new-password"
                    />
                </Campo>
                <Campo rotulo="Confirmar senha">
                    <input
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        minLength={8}
                        autoComplete="new-password"
                    />
                </Campo>
            </div>
        </>
    );
}
