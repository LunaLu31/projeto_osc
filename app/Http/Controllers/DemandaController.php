<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Models\Osc;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index()
    {
        $demandas = Demanda::with('osc')->latest('data_criacao')->get()->map(function (Demanda $demanda) {
            $nomeOsc = $demanda->osc?->nome ?? 'Organização';

            return [
                'id' => $demanda->id_demanda,
                'title' => $demanda->titulo,
                'organization' => $nomeOsc,
                'initials' => collect(explode(' ', trim($nomeOsc)))
                    ->filter()
                    ->take(2)
                    ->map(fn($parte) => mb_substr($parte, 0, 1))
                    ->implode(''),
                'category' => $demanda->categoria ?? 'Geral',
                'description' => $demanda->descricao ?? '',
                'quantity' => $demanda->quantidade ?? 'A combinar',
                'deadline' => $demanda->prazo
                    ? \Illuminate\Support\Carbon::parse($demanda->prazo)->format('d/m/Y')
                    : 'Sem prazo',
                'neighborhood' => $demanda->osc?->endereco ?? 'Não informado',
                'status' => ucfirst($demanda->status ?? 'aberta'),
                'urgent' => mb_strtolower($demanda->urgencia ?? '') === 'urgente',
                'progress' => 0,
            ];
        });

        $completedStatuses = ['concluida', 'concluída', 'concluido', 'concluído', 'atendida', 'atendido', 'finalizada', 'finalizado'];
        $closedStatuses = [...$completedStatuses, 'cancelada', 'cancelado', 'encerrada', 'encerrado'];
        $totalOscs = Osc::count();
        $activeDemands = Demanda::whereNotNull('status')
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), $closedStatuses)
            ->count();
        $completedDemands = Demanda::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), $completedStatuses)->count();
        $totalInterests = ManifestacaoInteresse::count();
        $monthlyInterests = ManifestacaoInteresse::where('data_manifestacao', '>=', now()->startOfMonth())->count();
        $featuredOscInitials = Osc::query()
            ->orderBy('nome')
            ->limit(3)
            ->get(['nome'])
            ->map(fn(Osc $osc) => collect(explode(' ', trim($osc->nome)))
                ->filter()
                ->take(2)
                ->map(fn(string $parte) => mb_substr($parte, 0, 1))
                ->implode(''));

        $osc = Osc::find(session('osc_id'));
        if (! $osc && session()->has('osc_id')) {
            session()->forget('osc_id');
        }

        $authenticatedOsc = $osc ? [
            'id' => $osc->id_osc,
            'nome' => $osc->nome,
            'initials' => collect(explode(' ', trim($osc->nome)))
                ->filter()
                ->take(2)
                ->map(fn(string $parte) => mb_substr($parte, 0, 1))
                ->implode(''),
        ] : null;

        return view('feed', compact(
            'demandas',
            'authenticatedOsc',
            'totalOscs',
            'activeDemands',
            'completedDemands',
            'totalInterests',
            'monthlyInterests',
            'featuredOscInitials',
        ));
    }
    public function store(Request $request)
    {
        Demanda::create($request->all() + ['id_osc' => 1, 'status' => 'ativa']);
        return redirect('/');
    }
}
