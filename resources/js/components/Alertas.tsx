import { usePropsPagina } from '@/lib/pagina';
import Icone from './Icone';

/** Mensagens de retorno das ações (flash do Laravel) e resumo de erros de validação. */
export default function Alertas({ resumoErros = true }: { resumoErros?: boolean }) {
    const { flash, errors } = usePropsPagina();

    return (
        <>
            {flash.status && (
                <p className="alert alert--success" role="status">
                    <Icone nome="check" /> {flash.status}
                </p>
            )}
            {flash.erro && (
                <p className="alert alert--error" role="alert">
                    <Icone nome="warning" /> {flash.erro}
                </p>
            )}
            {resumoErros && Object.keys(errors).length > 0 && (
                <div className="alert alert--error" role="alert">
                    <Icone nome="warning" />
                    <span>Não foi possível salvar. Revise os campos destacados.</span>
                </div>
            )}
        </>
    );
}
