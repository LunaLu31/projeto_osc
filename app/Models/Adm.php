<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Adm extends Model
{
    protected $table = 'adms';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;
    protected $fillable = ['nome', 'email', 'senha_hash','data_cadastro'];

    public function oscs()
    {
        return $this->hasMany(Osc::class, 'id_admin', 'id_admin');
    }
}