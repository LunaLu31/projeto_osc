<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminPortalController extends Controller
{
    public function entry(Request $request)
    {
        if ($this->currentAdmin($request)) {
            return redirect('/admin/dashboard');
        }

        return Adm::query()->exists()
            ? response()->view('admin.login')
            : response()->view('admin.setup');
    }

    public function setup()
    {
        if (Adm::query()->exists()) {
            return redirect('/admin')->withErrors([
                'email' => 'A configuração inicial já foi concluída. Entre com sua conta administrativa.',
            ]);
        }

        return view('admin.setup');
    }

    public function storeFirstAdmin(Request $request)
    {
        if (Adm::query()->exists()) {
            return redirect('/admin')->withErrors([
                'email' => 'A configuração inicial já foi concluída.',
            ]);
        }

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:adms,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = Adm::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha_hash' => Hash::make($data['password']),
        ]);

        $this->startAdminSession($request, $admin);

        return redirect('/admin/dashboard');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Adm::where('email', $data['email'])->first();

        if (! $admin || ! Hash::check($data['password'], $admin->senha_hash)) {
            return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])
                ->withInput($request->only('email'));
        }

        $this->startAdminSession($request, $admin);

        return redirect('/admin/dashboard');
    }

    public function dashboard(Request $request)
    {
        $admin = $this->currentAdmin($request);

        if (! $admin) {
            return redirect('/admin');
        }

        $oscs = $admin->oscs()->withCount('demandas')->orderBy('nome')->get();
        $demandas = Demanda::whereHas('osc', fn($query) => $query->where('id_admin', $admin->id_admin))
            ->with('osc')
            ->withCount('manifestacoes')
            ->latest('data_criacao')
            ->get();

        return view('admin.dashboard', compact('admin', 'oscs', 'demandas'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('adm_id');
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect('/admin');
    }

    private function currentAdmin(Request $request): ?Adm
    {
        $admin = Adm::find($request->session()->get('adm_id'));

        if (! $admin && $request->session()->has('adm_id')) {
            $request->session()->forget('adm_id');
        }

        return $admin;
    }

    private function startAdminSession(Request $request, Adm $admin): void
    {
        $request->session()->regenerate();
        $request->session()->forget('osc_id');
        $request->session()->put('adm_id', $admin->id_admin);
    }
}
