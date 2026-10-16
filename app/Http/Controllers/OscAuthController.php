<?php

namespace App\Http\Controllers;

use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OscAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $osc = Osc::where('email', $credentials['email'])->first();

        if (! $osc || ! Hash::check($credentials['password'], $osc->senha_hash)) {
            return response()->json([
                'message' => 'As credenciais informadas são inválidas.',
                'errors' => ['email' => ['E-mail ou senha inválidos.']],
            ], 422);
        }

        $request->session()->regenerate();
        $request->session()->forget('adm_id');
        $request->session()->put('osc_id', $osc->id_osc);

        return response()->json([
            'osc' => [
                'id' => $osc->id_osc,
                'nome' => $osc->nome,
                'initials' => collect(explode(' ', trim($osc->nome)))
                    ->filter()
                    ->take(2)
                    ->map(fn(string $parte) => mb_substr($parte, 0, 1))
                    ->implode(''),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('osc_id');
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
