<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Troca da senha inicial gerada pelo administrador (RF02).
 */
class SenhaController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Osc/Senha');
    }

    public function update(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'senha_atual' => ['required', 'current_password:osc'],
            'password' => ['required', 'confirmed', 'different:senha_atual', Password::defaults()],
        ]);

        $osc = $request->user('osc');
        $osc->update(['senha_hash' => Hash::make($dados['password'])]);
        // Tokens da API emitidos com a senha antiga deixam de valer.
        $osc->tokens()->delete();
        $request->session()->regenerate();

        return redirect()->route('osc.painel')->with('status', 'Senha alterada com sucesso.');
    }
}
