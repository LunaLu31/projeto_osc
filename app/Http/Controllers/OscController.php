<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OscController extends Controller
{
    public function store(Request $request)
    {
        $admin = Adm::find($request->session()->get('adm_id'));

        if (! $admin) {
            return redirect('/admin');
        }

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'area_atuacao' => ['nullable', 'string', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', 'unique:oscs,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->oscs()->create([
            'nome' => $data['nome'],
            'area_atuacao' => $data['area_atuacao'] ?? null,
            'descricao' => $data['descricao'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'telefone' => $data['telefone'] ?? null,
            'email' => $data['email'],
            'senha_hash' => Hash::make($data['password']),
        ]);

        return redirect('/admin/dashboard')->with('status', 'OSC cadastrada com sucesso.');
    }
}
