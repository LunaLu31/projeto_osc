import { Head } from '@inertiajs/react';
import marca from '../../images/redcam-marca.png';

const mensagens: Record<number, [string, string]> = {
    403: ['Acesso negado', 'Você não tem permissão para acessar esta página.'],
    404: ['Página não encontrada', 'O endereço acessado não existe ou foi removido.'],
    429: ['Muitas tentativas', 'Você fez muitas tentativas em pouco tempo. Aguarde um minuto.'],
    500: ['Algo deu errado', 'Ocorreu um erro ao processar sua solicitação. Tente novamente em instantes.'],
    503: ['Em manutenção', 'O sistema está em manutenção. Volte em alguns minutos.'],
};

/**
 * Página de erro amigável (RNF12): nunca exibe mensagens técnicas ao usuário.
 * Autossuficiente: rotas inexistentes não passam pelo middleware que compartilha
 * props (rotas do Ziggy, usuário logado), então não depende delas.
 */
export default function Erro({ status }: { status: number }) {
    const [titulo, descricao] = mensagens[status] ?? mensagens[500];

    return (
        <>
            <Head title={titulo} />
            <main id="conteudo" className="auth-shell">
                <div className="auth-card px-[22px] py-[30px] sm:p-[42px]">
                    <a href="/" className="logo">
                        <span className="logo__mark">
                            <img src={marca} alt="" width={42} height={42} />
                        </span>
                        <span className="logo__copy">
                            <strong>Redcam</strong>
                            <small>Rede que transforma</small>
                        </span>
                    </a>
                    <span className="eyebrow">Erro {status}</span>
                    <h1>{titulo}</h1>
                    <p>{descricao}</p>
                    <a href="/" className="button button--primary button--block mt-6">
                        Voltar para o início
                    </a>
                </div>
            </main>
        </>
    );
}
