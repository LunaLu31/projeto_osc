<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OscRequest;
use App\Http\Resources\V1\OscResource;
use App\Models\Osc;
use App\Services\GestaoOscs;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Cadastro e gestão de OSCs pelo administrador (RF02/RF09).
 */
class OscController extends Controller
{
    public function store(OscRequest $request, GestaoOscs $gestao): RedirectResponse
    {
        [, $senhaGerada] = $gestao->cadastrar($request->user('admin'), $request->dadosOsc(), $request->validated('password'));

        return redirect()->route('admin.painel')
            ->with('status', 'OSC cadastrada com sucesso.')
            ->with('senha_inicial', $senhaGerada);
    }

    public function edit(Osc $osc): Response
    {
        return Inertia::render('Admin/OscForm', ['osc' => OscResource::make($osc)->resolve()]);
    }

    public function update(OscRequest $request, Osc $osc, GestaoOscs $gestao): RedirectResponse
    {
        $gestao->atualizar($osc, $request->dadosOsc(), $request->validated('password'));

        return redirect()->route('admin.painel')->with('status', 'OSC atualizada com sucesso.');
    }

    public function destroy(Osc $osc, GestaoOscs $gestao): RedirectResponse
    {
        $gestao->excluir($osc);

        return redirect()->route('admin.painel')->with('status', 'OSC excluída com suas demandas e manifestações.');
    }
}
