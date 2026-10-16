<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ManifestacaoInteresse extends Model
{
    protected $table = 'manifestacoes_interesses';
    protected $primaryKey = 'id_manifestacao';
    public $timestamps = false;
    protected $fillable = ['id_demanda','nome_apoiador','telefone','email','tipo_interesse','mensagem'];

    public function demanda()
    {
        return $this->belongsTo(Demanda::class, 'id_demanda', 'id_demanda');
    }
}