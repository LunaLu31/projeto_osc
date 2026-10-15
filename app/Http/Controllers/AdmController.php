<?php
namespace App\Http\Controllers;
use App\Models\Adm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdmController extends Controller
{
    public function index() {
        return view('adms.index', ['adms' => Adm::all()]);
    }
    public function create() {
        return view('adms.create');
    }
    public function store(Request $request) {
        $request->validate([
            'nome' => 'required',
            'email' => 'required|email|unique:adms,email',
            'senha' => 'required'
        ]);
        Adm::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha_hash' => Hash::make($request->senha)
        ]);
        return redirect('/adms');
    }
}