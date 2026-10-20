<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Moderação: o administrador pode intervir em qualquer demanda (RF07).
 */
class DemandaController extends Controller
{
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
