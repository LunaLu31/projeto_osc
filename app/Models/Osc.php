<?php

namespace App\Models;

use App\Models\Concerns\TemIniciais;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Usuário autenticável pelo guard `osc` (config/auth.php).
 */
class Osc extends Authenticatable
{
    use HasFactory, TemIniciais;

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
}
