<?php

namespace App\Http\Requests;

use App\Models\ManifestacaoInteresse;
use App\Rules\EnvioHumano;
use App\Rules\TurnstileValido;
use App\Support\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Formulário público de manifestação de interesse (RF05). Não exige login.
 * A demanda vem da rota: /demandas/{demanda}/interesse.
 */
class ManifestacaoInteresseRequest extends FormRequest
{
    public function rules(): array
    {
        $idDemanda = $this->route('demanda')->id_demanda;

        return $this->regrasAntiSpam() + [
            'nome_apoiador' => ['required', 'string', 'max:100'],
            'telefone' => [
                'nullable', 'required_without:email', 'string', 'max:20',
                Rule::unique('manifestacoes_interesses', 'telefone')->where('id_demanda', $idDemanda),
            ],
            'email' => [
                'nullable', 'required_without:telefone', 'email', 'max:150',
                Rule::unique('manifestacoes_interesses', 'email')->where('id_demanda', $idDemanda),
            ],
            'tipo_interesse' => ['required', Rule::in(ManifestacaoInteresse::TIPOS)],
            'mensagem' => ['nullable', 'string', 'max:2000'],
            'consentimento' => ['accepted'],
        ];
    }

    /**
     * RNF – proteção contra envios automatizados (formulário público, sem login):
     * honeypot (campo invisível que deve ficar vazio), armadilha de tempo e Turnstile.
     */
    private function regrasAntiSpam(): array
    {
        $regras = [
            'website' => ['prohibited'],
            '_carregado_em' => ['required', new EnvioHumano],
        ];

        if (Turnstile::ativo()) {
            $regras[Turnstile::CAMPO] = ['required', new TurnstileValido($this->ip())];
        }

        return $regras;
    }

    public function messages(): array
    {
        return [
            'telefone.unique' => 'Você já manifestou interesse nesta demanda com este telefone.',
            'email.unique' => 'Você já manifestou interesse nesta demanda com este e-mail.',
            'telefone.required_without' => 'Informe ao menos um meio de contato: telefone ou e-mail.',
            'email.required_without' => 'Informe ao menos um meio de contato: telefone ou e-mail.',
            'consentimento.accepted' => 'É preciso autorizar o compartilhamento dos seus dados com a OSC.',
            'website.prohibited' => 'Não foi possível enviar o formulário. Tente novamente.',
            Turnstile::CAMPO.'.required' => 'Confirme a verificação de segurança antes de enviar.',
            '_carregado_em.required' => 'Não foi possível enviar o formulário. Atualize a página e tente novamente.',
        ];
    }

    /**
     * Dados para gravação (o consentimento é apenas validado).
     */
    public function dadosManifestacao(): array
    {
        return $this->safe()->only([
            'nome_apoiador', 'telefone', 'email', 'tipo_interesse', 'mensagem',
        ]);
    }
}
