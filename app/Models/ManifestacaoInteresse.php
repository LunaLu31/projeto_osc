<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManifestacaoInteresse extends Model
{
    use HasFactory;

    /** Valores do ENUM `tipo_interesse`. */
    public const TIPOS = ['Apoiador/Doação', 'Voluntário', 'Aluno'];

    protected $table = 'manifestacoes_interesses';

    protected $primaryKey = 'id_manifestacao';

    public $timestamps = false;

    protected $fillable = ['id_demanda', 'nome_apoiador', 'telefone', 'email', 'tipo_interesse', 'mensagem'];

    protected function casts(): array
    {
        return [
            'data_manifestacao' => 'datetime',
        ];
    }

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class, 'id_demanda', 'id_demanda');
    }
}
