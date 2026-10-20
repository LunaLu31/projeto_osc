<?php

namespace App\Http\Requests;

use App\Models\ManifestacaoInteresse;
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

        return [
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

    public function messages(): array
    {
        return [
            'telefone.unique' => 'Você já manifestou interesse nesta demanda com este telefone.',
            'email.unique' => 'Você já manifestou interesse nesta demanda com este e-mail.',
            'telefone.required_without' => 'Informe ao menos um meio de contato: telefone ou e-mail.',
            'email.required_without' => 'Informe ao menos um meio de contato: telefone ou e-mail.',
            'consentimento.accepted' => 'É preciso autorizar o compartilhamento dos seus dados com a OSC.',
        ];
    }

    /**
     * Dados para gravação (o consentimento é apenas validado).
     */
    public function dadosManifestacao(): array
    {
        return collect($this->validated())->except('consentimento')->all();
    }
}
