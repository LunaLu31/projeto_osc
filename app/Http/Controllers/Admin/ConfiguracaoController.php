<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Criação do primeiro administrador. Disponível apenas enquanto não houver nenhum.
 */
class ConfiguracaoController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        if (Adm::query()->exists()) {
            return redirect()->route('admin.login');
        }

        return Inertia::render('Admin/Configuracao');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Adm::query()->exists()) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'A configuração inicial já foi concluída.']);
        }

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:adms,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin = Adm::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha_hash' => Hash::make($dados['password']),
        ]);

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.painel');
    }
}
