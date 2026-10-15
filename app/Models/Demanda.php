<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Demanda extends Model
{
    protected $table = 'demandas';
    protected $primaryKey = 'id_demanda';
    public $timestamps = true; // pra usar data_atualizacao
    protected $fillable = ['id_osc','titulo','categoria','descricao','quantidade','prazo','status','urgencia'];

    public function osc()
    {
        return $this->belongsTo(Osc::class, 'id_osc', 'id_osc');
    }
    public function manifestacoes()
    {
        return $this->hasMany(ManifestacaoInteresse::class, 'id_demanda', 'id_demanda');
    }
}