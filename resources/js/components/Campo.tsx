import type { ReactNode } from 'react';

interface CampoProps {
    rotulo: string;
    erro?: string;
    dica?: string;
    opcional?: boolean;
    className?: string;
    children: ReactNode;
}

/** Campo de formulário com rótulo, dica e mensagem de validação vinda do Laravel. */
export default function Campo({ rotulo, erro, dica, opcional = false, className, children }: CampoProps) {
    const classes = ['field', erro ? 'field--invalid' : null, className].filter(Boolean).join(' ');

    return (
        <label className={classes}>
            <span className="field__label">
                {rotulo} {opcional && <span className="field__optional">(opcional)</span>}
            </span>
            {children}
            {dica && <span className="field__hint">{dica}</span>}
            {erro && (
                <span className="field__error" role="alert">
                    {erro}
                </span>
            )}
        </label>
    );
}
