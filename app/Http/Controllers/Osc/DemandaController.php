<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandaRequest;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CRUD de demandas da OSC autenticada (RF03) e atualização de status (RF07).
 * A DemandaPolicy garante que a OSC só altera as próprias demandas.
 */
class DemandaController extends Controller
{
    /**
     * Gestão das demandas da OSC, com filtro por status.
     */
    public function index(Request $request): Response
    {
        $osc = $request->user('osc');
        $status = array_key_exists((string) $request->query('status'), Demanda::STATUS) ? $request->query('status') : null;

        return Inertia::render('Osc/Demandas', [
            'demandas' => DemandaResource::collection($osc->demandas()
                ->withCount('manifestacoes')
                ->when($status, fn ($q, $status) => $q->where('status', $status))
                ->latest('data_criacao')
                ->get())->resolve(),
            'statusAtual' => $status,
            'totaisPorStatus' => $osc->demandas()->toBase()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Osc/DemandaForm', ['demanda' => null]);
    }

    public function store(DemandaRequest $request): RedirectResponse
    {
        $request->user('osc')->demandas()->create($request->dadosDemanda());

        return redirect()->route('osc.demandas.index')->with('status', 'Demanda publicada com sucesso.');
    }

    public function edit(Demanda $demanda): Response
    {
        Gate::authorize('gerenciar', $demanda);

        return Inertia::render('Osc/DemandaForm', ['demanda' => DemandaResource::make($demanda)->resolve()]);
    }

    public function update(DemandaRequest $request, Demanda $demanda): RedirectResponse
    {
        Gate::authorize('gerenciar', $demanda);

        $demanda->update($request->dadosDemanda());

        return redirect()->route('osc.demandas.index')->with('status', 'Demanda atualizada.');
    }

    public function updateStatus(Request $request, Demanda $demanda): RedirectResponse
    {
        Gate::authorize('gerenciar', $demanda);

        $demanda->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Demanda::STATUS))],
        ]));

        return back()->with('status', 'Status atualizado.');
    }

    public function destroy(Demanda $demanda): RedirectResponse
    {
        Gate::authorize('gerenciar', $demanda);

        $demanda->delete();

        return redirect()->route('osc.demandas.index')->with('status', 'Demanda excluída.');
    }
}
