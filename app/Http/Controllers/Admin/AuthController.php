<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Adm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        // Sem nenhum administrador, o primeiro acesso cria a conta.
        if (! Adm::query()->exists()) {
            return redirect()->route('admin.configuracao');
        }

        return Inertia::render('Admin/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar('admin');

        return redirect()->intended(route('admin.painel'));
    }

    /** Revoga o JWT do login (e apaga o cookie); a sessão guarda só CSRF e mensagens. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
