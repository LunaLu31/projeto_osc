<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Osc/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar('osc');

        return redirect()->intended(route('osc.painel'));
    }

    /** Revoga o JWT do login (e apaga o cookie); a sessão guarda só CSRF e mensagens. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('osc')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('feed');
    }
}
