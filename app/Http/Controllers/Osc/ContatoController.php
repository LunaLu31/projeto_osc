<?php

namespace App\Http\Controllers\Osc;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ContatoResource;
use App\Models\ManifestacaoInteresse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Contatos recebidos pela OSC em todas as suas demandas (RF08).
 * Dados pessoais visíveis apenas no painel privado da OSC responsável (RNF06).
 */
class ContatoController extends Controller
{
    private const POR_PAGINA = 20;

    public function index(Request $request): Response
    {
        $osc = $request->user('osc');
        $demandas = $osc->demandas()->orderBy('titulo')->get(['id_demanda', 'id_osc', 'titulo']);

        // Filtros inválidos ou de demandas de outra OSC são ignorados.
        $filtros = [
            'demanda' => $demandas->firstWhere('id_demanda', (int) $request->query('demanda'))?->id_demanda,
            'tipo' => in_array($request->query('tipo'), ManifestacaoInteresse::TIPOS, true) ? $request->query('tipo') : null,
        ];

        return Inertia::render('Osc/Contatos', [
            'contatos' => ContatoResource::collection($osc->manifestacoes()
                ->with('demanda:id_demanda,titulo')
                ->filtrar($filtros['demanda'], $filtros['tipo'])
                ->latest('data_manifestacao')
                ->paginate(self::POR_PAGINA)
                ->withQueryString()),
            'demandas' => $demandas->map(fn ($demanda) => ['id' => $demanda->id_demanda, 'titulo' => $demanda->titulo]),
            'filtros' => $filtros,
        ]);
    }
}
