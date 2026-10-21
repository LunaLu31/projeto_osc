<?php

namespace App\Http\Middleware;

use App\Models\Demanda;
use App\Models\ManifestacaoInteresse;
use App\Support\Tema;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

/**
 * Ponte entre o Laravel e o front-end React: define a view raiz e as props
 * compartilhadas por todas as páginas (resources/js/types/index.d.ts).
 */
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'app' => [
                'nome' => config('app.name'),
            ],
            // Somente dados de exibição; nunca e-mail ou senha.
            'auth' => fn () => [
                'osc' => $this->usuario($request, 'osc'),
                'admin' => $this->usuario($request, 'admin'),
            ],
            'flash' => fn () => [
                'status' => $request->session()->get('status'),
                'erro' => $request->session()->get('erro'),
                'senhaInicial' => $request->session()->get('senha_inicial'),
            ],
            'tema' => Tema::atual($request),
            'referencias' => [
                'categorias' => Demanda::CATEGORIAS,
                'status' => Demanda::STATUS,
                'statusPublicos' => Demanda::STATUS_PUBLICOS,
                'tiposInteresse' => ManifestacaoInteresse::TIPOS,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }

    /**
     * @return array{id: int, nome: string, iniciais: string}|null
     */
    private function usuario(Request $request, string $guard): ?array
    {
        $usuario = $request->user($guard);

        return $usuario ? [
            'id' => $usuario->getAuthIdentifier(),
            'nome' => $usuario->nome,
            'iniciais' => $usuario->iniciais,
        ] : null;
    }
}
