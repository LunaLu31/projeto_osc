<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OscRequest;
use App\Models\Osc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Cadastro e gestão de OSCs pelo administrador (RF02/RF09).
 */
class OscController extends Controller
{
    public function store(OscRequest $request): RedirectResponse
    {
        $senhaInicial = $request->validated('password') ?: Str::password(12, symbols: false);

        $request->user('admin')->oscs()->create($request->dadosOsc() + [
            'senha_hash' => Hash::make($senhaInicial),
        ]);

        return redirect()->route('admin.painel')
            ->with('status', 'OSC cadastrada com sucesso.')
            ->with('senha_inicial', $senhaInicial);
    }

    public function edit(Osc $osc): View
    {
        return view('admin.osc-form', ['osc' => $osc]);
    }

    public function update(OscRequest $request, Osc $osc): RedirectResponse
    {
        $dados = $request->dadosOsc();

        if ($novaSenha = $request->validated('password')) {
            $dados['senha_hash'] = Hash::make($novaSenha);
        }

        $osc->update($dados);

        return redirect()->route('admin.painel')->with('status', 'OSC atualizada com sucesso.');
    }

    public function destroy(Osc $osc): RedirectResponse
    {
        $osc->delete();

        return redirect()->route('admin.painel')->with('status', 'OSC excluída com suas demandas e manifestações.');
    }
}
