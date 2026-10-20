<?php

namespace App\Models;

use App\Models\Concerns\TemIniciais;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Usuário autenticável pelo guard `osc` (config/auth.php)
 * e por tokens Bearer da API (Laravel Sanctum).
 */
class Osc extends Authenticatable
{
    use HasApiTokens, HasFactory, TemIniciais;

    /** Coluna da senha hasheada com password_hash()/bcrypt (RNF05). */
    protected $authPasswordName = 'senha_hash';

    /** O esquema não tem coluna remember_token. */
    protected $rememberTokenName = '';

    protected $table = 'oscs';

    protected $primaryKey = 'id_osc';

    public $timestamps = false;

    protected $fillable = ['id_admin', 'nome', 'area_atuacao', 'descricao', 'endereco', 'telefone', 'email', 'senha_hash'];

    protected $hidden = ['senha_hash'];

    public function adm(): BelongsTo
    {
        return $this->belongsTo(Adm::class, 'id_admin', 'id_admin');
    }

    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class, 'id_osc', 'id_osc');
    }

    /**
     * Contatos recebidos em todas as demandas da OSC (painel da OSC).
     */
    public function manifestacoes(): HasManyThrough
    {
        return $this->hasManyThrough(
            ManifestacaoInteresse::class,
            Demanda::class,
            'id_osc',
            'id_demanda',
            'id_osc',
            'id_demanda',
        );
    }
}
