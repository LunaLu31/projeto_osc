<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManifestacaoInteresseRequest;
use App\Models\Demanda;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManifestacaoInteresseController extends Controller
{
    /**
     * Registra o interesse do apoiador, sem login (RF05), vinculado à demanda (RF06).
     */
    public function store(ManifestacaoInteresseRequest $request, Demanda $demanda): RedirectResponse
    {
        abort_unless($demanda->estaPublica(), 404);

        try {
            $demanda->manifestacoes()->create($request->dadosManifestacao());
        } catch (UniqueConstraintViolationException) {
            // Envio duplicado simultâneo que escapou da validação.
            throw ValidationException::withMessages(['email' => 'Você já manifestou interesse nesta demanda.']);
        }

        return redirect()
            ->route('demandas.obrigado', $demanda)
            ->with('interesse_registrado', $demanda->id_demanda);
    }

    /**
     * Tela de sucesso exibida após o envio (RF06).
     */
    public function obrigado(Demanda $demanda): View|RedirectResponse
    {
        if ((int) session('interesse_registrado') !== (int) $demanda->id_demanda) {
            return redirect()->route('demandas.show', $demanda);
        }

        return view('publico.obrigado', ['demanda' => $demanda->load('osc:id_osc,nome')]);
    }
}
