<?php

namespace App\Http\Requests;

use App\Models\Demanda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validação de criação/edição de demandas (RF03).
 * A autorização é feita pelo middleware/controller.
 */
class DemandaRequest extends FormRequest
{
    public function rules(): array
    {
        $prazo = ['required', 'date'];

        // Ao criar, o prazo não pode estar no passado; ao editar, mantém-se o prazo original.
        if ($this->isMethod('post')) {
            $prazo[] = 'after_or_equal:today';
        }

        return [
            'titulo' => ['required', 'string', 'max:150'],
            'categoria' => ['required', 'string', Rule::in(Demanda::CATEGORIAS)],
            'quantidade' => ['required', 'integer', 'min:1', 'max:1000000'],
            'descricao' => ['required', 'string', 'max:5000'],
            'prazo' => $prazo,
            'urgencia' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Dados prontos para gravação: checkbox desmarcado vira `false`.
     */
    public function dadosDemanda(): array
    {
        return ['urgencia' => $this->boolean('urgencia')] + $this->validated();
    }
}
