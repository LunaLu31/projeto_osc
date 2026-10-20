<?php

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ManifestacaoInteresseApiRequest;
use App\Models\Demanda;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

#[Group('Público', 'Consulta de demandas e manifestação de interesse, sem login.', weight: 1)]
class ManifestacaoInteresseController extends Controller
{
    /**
     * Manifestar interesse
     *
     * Registra o interesse de um apoiador em uma demanda aberta (RF05/RF06). Não exige login.
     * Informe ao menos um contato (telefone ou e-mail) e o consentimento LGPD.
     * O mesmo telefone ou e-mail só pode manifestar interesse uma vez por demanda.
     * Limite: 10 requisições por minuto por IP.
     *
     * @unauthenticated
     */
    public function store(ManifestacaoInteresseApiRequest $request, Demanda $demanda): JsonResponse
    {
        abort_unless($demanda->estaPublica(), 404);

        try {
            $manifestacao = $demanda->manifestacoes()->create($request->dadosManifestacao());
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Você já manifestou interesse nesta demanda.']);
        }

        return response()->json([
            'message' => 'Interesse registrado! A OSC vai entrar em contato.',
            'data' => [
                'id' => $manifestacao->id_manifestacao,
                'demanda_id' => $demanda->id_demanda,
            ],
        ], 201);
    }
}
