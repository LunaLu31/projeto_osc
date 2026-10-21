import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { route } from 'ziggy-js';
import CamposOsc, { type DadosOsc } from '@/components/CamposOsc';
import CartaoPainel from '@/components/CartaoPainel';
import Icone from '@/components/Icone';

const oscVazia: DadosOsc = {
    nome: '',
    area_atuacao: '',
    telefone: '',
    endereco: '',
    descricao: '',
    email: '',
    password: '',
    password_confirmation: '',
};

/** Cadastro de OSC pelo administrador (RF02). */
export default function FormularioCadastroOsc() {
    const formulario = useForm<DadosOsc>(oscVazia);

    const cadastrar = (evento: FormEvent) => {
        evento.preventDefault();
        formulario.post(route('admin.oscs.store'), { preserveScroll: true, onSuccess: () => formulario.reset() });
    };

    return (
        <CartaoPainel>
            <div className="section-heading--compact">
                <div>
                    <span className="eyebrow">Cadastro</span>
                    <h2>Cadastrar OSC</h2>
                </div>
            </div>
            <form onSubmit={cadastrar}>
                <CamposOsc formulario={formulario} editando={false} />
                <div className="mt-[26px] flex">
                    <button
                        type="submit"
                        className="button button--primary button--block"
                        disabled={formulario.processing}
                    >
                        <Icone nome="plus" /> Cadastrar organização
                    </button>
                </div>
            </form>
        </CartaoPainel>
    );
}
