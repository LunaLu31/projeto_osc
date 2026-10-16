<?php

namespace App\Http\Controllers;

use App\Models\ManifestacaoInteresse;
use Illuminate\Http\Request;


class ManifestacaoInteresseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_demanda' => 'required|exists:demandas,id_demanda',
            'nome_apoiador' => 'required|string|max:150',
            'telefone' => 'nullable|required_without:email|string|max:20',
            'email' => 'nullable|required_without:telefone|email|max:150',
            'tipo_interesse' => 'required|string|max:50',
            'mensagem' => 'nullable|string',
        ]);

        ManifestacaoInteresse::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Interesse registrado! A OSC vai entrar em contato.'], 201);
        }

        return back()->with('success', 'Interesse registrado! A OSC vai entrar em contato.');
    }

    public function index($id_demanda)
    {
        $manifestacoes = ManifestacaoInteresse::where('id_demanda', $id_demanda)->get();
        return view('manifestacoes.index', compact('manifestacoes'));
    }
}
