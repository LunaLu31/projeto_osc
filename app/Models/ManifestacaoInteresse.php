<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Filtros dos contatos no painel da OSC (site e API).
     * A coluna é qualificada porque a consulta passa pela tabela de demandas.
     */
    public function scopeFiltrar(Builder $query, ?int $idDemanda = null, ?string $tipo = null): Builder
    {
        return $query
            ->when($idDemanda, fn (Builder $q) => $q->where($this->qualifyColumn('id_demanda'), $idDemanda))
            ->when($tipo, fn (Builder $q) => $q->where($this->qualifyColumn('tipo_interesse'), $tipo));
    }

    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class, 'id_demanda', 'id_demanda');
    }
}
