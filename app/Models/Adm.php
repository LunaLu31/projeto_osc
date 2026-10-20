<?php

namespace App\Models;

use App\Models\Concerns\TemIniciais;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Usuário autenticável pelo guard `admin` (config/auth.php)
 * e por tokens Bearer da API (Laravel Sanctum).
 */
class Adm extends Authenticatable
{
    use HasApiTokens, HasFactory, TemIniciais;

    /** Coluna da senha hasheada com password_hash()/bcrypt (RNF05). */
    protected $authPasswordName = 'senha_hash';

    /** O esquema não tem coluna remember_token. */
    protected $rememberTokenName = '';

    protected $table = 'adms';

    protected $primaryKey = 'id_admin';

    public $timestamps = false;

    protected $fillable = ['nome', 'email', 'senha_hash'];

    protected $hidden = ['senha_hash'];

    public function oscs(): HasMany
    {
        return $this->hasMany(Osc::class, 'id_admin', 'id_admin');
    }
}
