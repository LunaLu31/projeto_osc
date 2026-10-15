<?php

namespace App\Http\Controllers;
use App\Models\ManifestacaoInteresse;
use Illuminate\Http\Request;


 class ManifestacaoInteresseController extends Controller
 {
    public function store(Request $request){
        $request->validate([
            'id_demanda' => 'required|exists:demandas,id_demanda',
            'nome_apoiador' => 'required',
            'telefone' => 'required',
            'tipo_interesse' => 'required'
        ]);

        ManifestacaoInteresse::create($request->all());

        return back()->with('success', 'Interesse registrado! A OSC vai entrar em contato.');
    }

    public function index($id_demanda){
        $manifestacoes = ManifestacaoInteresse::where('id_demanda', $id_demanda)->get();
        return view('manifestacoes.index', compact('manifestacoes'));
    }
}
