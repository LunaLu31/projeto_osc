<?php

namespace App\Services;

use App\Models\Adm;
use App\Models\Osc;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Cadastro e atualização de OSCs pelo administrador (RF02), usado pelo site e pela API.
 */
class GestaoOscs
{
    /**
     * Cadastra a OSC. Sem senha informada, gera uma senha inicial e a devolve
     * (deve ser exibida uma única vez ao administrador).
     *
     * @param  array<string, mixed>  $dados
     * @return array{0: Osc, 1: ?string} [OSC criada, senha gerada ou null]
     */
    public function cadastrar(Adm $admin, array $dados, ?string $senha = null): array
    {
        $senhaGerada = $senha ? null : Str::password(12, symbols: false);

        $osc = $admin->oscs()->create($dados + [
            'senha_hash' => Hash::make($senha ?: $senhaGerada),
        ]);

        return [$osc->refresh(), $senhaGerada];
    }

    /**
     * Atualiza os dados da OSC. Uma nova senha revoga os tokens da API já emitidos.
     *
     * @param  array<string, mixed>  $dados
     */
    public function atualizar(Osc $osc, array $dados, ?string $novaSenha = null): Osc
    {
        if ($novaSenha) {
            $dados['senha_hash'] = Hash::make($novaSenha);
            $osc->tokens()->delete();
        }

        $osc->update($dados);

        return $osc;
    }

    public function excluir(Osc $osc): void
    {
        $osc->tokens()->delete();
        $osc->delete();
    }
}
