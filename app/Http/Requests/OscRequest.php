<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validação do cadastro/edição de OSCs pelo administrador (RF02).
 */
class OscRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'area_atuacao' => ['required', 'string', 'max:100'],
            'descricao' => ['required', 'string', 'max:5000'],
            'endereco' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('oscs', 'email')->ignore($this->route('osc'), 'id_osc'),
            ],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Dados da OSC sem a senha (tratada separadamente pelo controller).
     */
    public function dadosOsc(): array
    {
        return collect($this->validated())->except(['password'])->all();
    }
}
