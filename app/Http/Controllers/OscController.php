<?php
namespace App\Http\Controllers;
use App\Models\Osc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OscController extends Controller
{
    public function store(Request $request){
        Osc::create([
            'id_admin' => 1,
            'nome' => $request->nome,
            'area_atuacao' => $request->area,
            'email' => $request->email,
            'senha_hash' => Hash::make($request->senha),
            'descricao' => $request->descricao
        ]);
        return redirect('/');
    }
}
