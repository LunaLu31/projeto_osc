<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Troca da senha inicial gerada pelo administrador (RF02).
 */
class SenhaController extends Controller
{
    public function edit(): View
    {
        return view('osc.senha');
    }

    public function update(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'senha_atual' => ['required', 'current_password:osc'],
            'password' => ['required', 'confirmed', 'different:senha_atual', Password::defaults()],
        ]);

        $request->user('osc')->update(['senha_hash' => Hash::make($dados['password'])]);
        $request->session()->regenerate();

        return redirect()->route('osc.painel')->with('status', 'Senha alterada com sucesso.');
    }
}
