<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DemandaResource;
use App\Models\Demanda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Moderação: o administrador pode intervir em qualquer demanda (RF07).
 */
class DemandaController extends Controller
{
    private const DEMANDAS_POR_PAGINA = 15;

    /**
     * Aba "Moderação": todas as demandas da rede, paginadas no servidor.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Moderacao', [
            'demandas' => DemandaResource::collection(Demanda::with('osc:id_osc,nome,area_atuacao,descricao,endereco')
                ->withCount('manifestacoes')
                ->latest('data_criacao')
                ->paginate(self::DEMANDAS_POR_PAGINA, pageName: 'pagina_demandas')
                ->withQueryString()),
        ]);
    }

    public function updateStatus(Request $request, Demanda $demanda): RedirectResponse
    {
        $demanda->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Demanda::STATUS))],
        ]));

        return back()->with('status', 'Status atualizado.');
    }

    public function destroy(Demanda $demanda): RedirectResponse
    {
        $demanda->delete();

        return back()->with('status', 'Demanda excluída.');
    }
}
