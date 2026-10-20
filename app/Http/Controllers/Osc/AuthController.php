<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('osc.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar('osc');

        return redirect()->intended(route('osc.painel'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('osc')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('feed');
    }
}
