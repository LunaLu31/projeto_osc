<?php
namespace App\Http\Controllers;
use App\Models\Demanda;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index(){
        $demandas = Demanda::with('osc')->latest('data_criacao')->get();
        return view('feed', compact('demandas'));
    }
    public function store(Request $request){
        Demanda::create($request->all() + ['id_osc' => 1, 'status' => 'ativa']);
        return redirect('/');
    }
}