import { router } from '@inertiajs/react';
import { useId, useRef, useState } from 'react';
import Icone from './Icone';

interface BotaoExcluirProps {
    url: string;
    /** Pergunta exibida no diálogo, ex.: Excluir a demanda “Cestas básicas”? */
    titulo: string;
    /** Consequência da exclusão, ex.: as manifestações recebidas também serão apagadas. */
    descricao: string;
}

/**
 * Exclusão com diálogo de confirmação acessível (<dialog> nativo: foco preso no
 * diálogo, Esc fecha e o foco volta ao botão). O foco inicial fica em "Cancelar".
 */
export default function BotaoExcluir({ url, titulo, descricao }: BotaoExcluirProps) {
    const dialogo = useRef<HTMLDialogElement>(null);
    const [excluindo, setExcluindo] = useState(false);
    const idTitulo = useId();
    const idDescricao = useId();

    const confirmar = () => {
        router.delete(url, {
            preserveScroll: true,
            onStart: () => setExcluindo(true),
            onFinish: () => {
                setExcluindo(false);
                dialogo.current?.close();
            },
        });
    };

    return (
        <>
            <button
                type="button"
                className="button button--danger button--small"
                onClick={() => dialogo.current?.showModal()}
            >
                <Icone nome="trash" tamanho={15} /> Excluir
            </button>
            <dialog
                ref={dialogo}
                className="dialogo"
                aria-labelledby={idTitulo}
                aria-describedby={idDescricao}
                // Clique no fundo escurecido fecha o diálogo.
                onClick={(evento) => evento.target === dialogo.current && dialogo.current.close()}
            >
                <h2 id={idTitulo}>{titulo}</h2>
                <p id={idDescricao}>{descricao}</p>
                <div className="mt-6 flex flex-col-reverse gap-2.5 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        className="button button--secondary"
                        onClick={() => dialogo.current?.close()}
                        autoFocus
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        className="button button--danger-solid"
                        onClick={confirmar}
                        disabled={excluindo}
                    >
                        {excluindo ? 'Excluindo…' : 'Excluir'}
                    </button>
                </div>
            </dialog>
        </>
    );
}
