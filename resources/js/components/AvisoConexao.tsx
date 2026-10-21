import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import Icone from './Icone';

const SEM_INTERNET = 'Você está sem internet. Verifique a conexão; seus dados não foram enviados.';
const FALHA_REDE = 'Não foi possível falar com o servidor. Verifique sua conexão e tente novamente.';
const ERRO_INESPERADO = 'Ocorreu um erro inesperado. Tente novamente em instantes.';

/**
 * Aviso global de falhas fora da validação (RNF12): sem internet, falha de rede
 * ou resposta inesperada do servidor. Os erros de validação (422) aparecem nos campos.
 */
export default function AvisoConexao() {
    const [mensagem, setMensagem] = useState<string | null>(null);

    useEffect(() => {
        const removerRede = router.on('networkError', (evento) => {
            evento.preventDefault();
            setMensagem(navigator.onLine ? FALHA_REDE : SEM_INTERNET);
        });

        // Em produção, nunca mostra o HTML cru de uma resposta de erro (padrão do Inertia).
        const removerHttp = router.on('httpException', (evento) => {
            if (import.meta.env.PROD) {
                evento.preventDefault();
                setMensagem(ERRO_INESPERADO);
            }
        });

        const removerSucesso = router.on('success', () => setMensagem(null));
        const ficouOffline = () => setMensagem(SEM_INTERNET);
        const voltouOnline = () => setMensagem((atual) => (atual === SEM_INTERNET ? null : atual));

        window.addEventListener('offline', ficouOffline);
        window.addEventListener('online', voltouOnline);

        return () => {
            removerRede();
            removerHttp();
            removerSucesso();
            window.removeEventListener('offline', ficouOffline);
            window.removeEventListener('online', voltouOnline);
        };
    }, []);

    if (!mensagem) {
        return null;
    }

    return (
        <div
            className="aviso-conexao alert alert--error fixed inset-x-4 bottom-4 z-[60] mx-auto mb-0 max-w-[560px]"
            role="alert"
        >
            <Icone nome="warning" />
            <span className="flex-1">{mensagem}</span>
            <button
                type="button"
                className="aviso-conexao__fechar"
                onClick={() => setMensagem(null)}
                aria-label="Fechar aviso"
            >
                <Icone nome="close" tamanho={18} />
            </button>
        </div>
    );
}
