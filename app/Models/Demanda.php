<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demanda extends Model
{
    use HasFactory;

    public const STATUS_ABERTA = 'Aberta';

    public const STATUS_PARCIAL = 'Parcial';

    public const STATUS_CONCLUIDA = 'Concluída';

    /** Valores do ENUM `status` => rótulo exibido na interface (RF07). */
    public const STATUS = [
        self::STATUS_ABERTA => 'Aberta',
        self::STATUS_PARCIAL => 'Parcialmente atendida',
        self::STATUS_CONCLUIDA => 'Concluída',
    ];

    /** Status exibidos na listagem pública; demandas concluídas saem do feed (RF07). */
    public const STATUS_PUBLICOS = [self::STATUS_ABERTA, self::STATUS_PARCIAL];

    public const CATEGORIAS = ['Alimentos', 'Materiais', 'Voluntariado', 'Vestuário', 'Higiene', 'Educação', 'Saúde', 'Outros'];

    public const CREATED_AT = 'data_criacao';

    public const UPDATED_AT = 'data_atualizacao';

    protected $table = 'demandas';

    protected $primaryKey = 'id_demanda';

    protected $fillable = ['id_osc', 'titulo', 'categoria', 'descricao', 'quantidade', 'prazo', 'status', 'urgencia'];

    protected $attributes = [
        'status' => self::STATUS_ABERTA,
        'urgencia' => false,
        'quantidade' => 1,
    ];

    protected function casts(): array
    {
        return [
            'prazo' => 'date',
            'urgencia' => 'boolean',
            'quantidade' => 'integer',
        ];
    }

    public function osc(): BelongsTo
    {
        return $this->belongsTo(Osc::class, 'id_osc', 'id_osc');
    }

    public function manifestacoes(): HasMany
    {
        return $this->hasMany(ManifestacaoInteresse::class, 'id_demanda', 'id_demanda');
    }

    public function scopePublicas(Builder $query): Builder
    {
        return $query->whereIn('status', self::STATUS_PUBLICOS);
    }

    /**
     * Filtros da listagem pública (RF04): busca, categoria, status e urgência.
     * Valores fora das listas permitidas são ignorados.
     *
     * @param  array{busca?: ?string, categoria?: ?string, status?: ?string, urgente?: bool}  $filtros
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        $busca = trim((string) ($filtros['busca'] ?? ''));

        return $query
            ->when($busca !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('titulo', 'like', "%{$busca}%")
                ->orWhereHas('osc', fn (Builder $q) => $q->where('nome', 'like', "%{$busca}%"))))
            ->when(in_array($filtros['categoria'] ?? null, self::CATEGORIAS, true),
                fn (Builder $q) => $q->where('categoria', $filtros['categoria']))
            ->when(in_array($filtros['status'] ?? null, self::STATUS_PUBLICOS, true),
                fn (Builder $q) => $q->where('status', $filtros['status']))
            ->when($filtros['urgente'] ?? false, fn (Builder $q) => $q->where('urgencia', true));
    }

    public function estaPublica(): bool
    {
        return in_array($this->status, self::STATUS_PUBLICOS, true);
    }

    public function rotuloStatus(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
