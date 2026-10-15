<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Osc extends Model
{
    protected $table = 'oscs';
    protected $primaryKey = 'id_osc';
    public $timestamps = false;
    protected $fillable = ['id_admin','nome','area_atuacao','descricao','endereco','telefone','email','senha_hash'];

    public function adm()
    {
        return $this->belongsTo(Adm::class, 'id_admin', 'id_admin');
    }
    public function demandas()
    {
        return $this->hasMany(Demanda::class, 'id_osc', 'id_osc');
    }
}