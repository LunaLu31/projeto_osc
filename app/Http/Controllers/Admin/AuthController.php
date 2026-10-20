<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Adm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        // Sem nenhum administrador, o primeiro acesso cria a conta.
        if (! Adm::query()->exists()) {
            return redirect()->route('admin.configuracao');
        }

        return view('admin.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar('admin');

        return redirect()->intended(route('admin.painel'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
