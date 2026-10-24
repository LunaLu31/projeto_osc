<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $guard = Auth::guard('osc');
        $osc = $guard->user();
        $lembrar = (bool) $guard->claims()?->lembrar;
        $osc->update(['senha_hash' => Hash::make($dados['password'])]);

        // A senha nova invalida todos os JWT já emitidos (site e API, em qualquer dispositivo);
        // este navegador recebe um token novo para continuar conectado.
        $guard->login($osc, $lembrar);
        $request->session()->regenerate();

        return redirect()->route('osc.painel')->with('status', 'Senha alterada com sucesso.');
    }
}
