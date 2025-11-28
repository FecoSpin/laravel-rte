<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampoFormativo extends Model
{
    use HasFactory;

    protected $table = 'campos_formativos';

    protected $fillable = [
        'formulario_id',
        'nombre'
    ];

    public function formulario()
    {
        return $this->belongsTo(FormularioInforme::class, 'formulario_id');
    }

    public function grados()
    {
        return $this->hasMany(GradoCampoFormativo::class, 'campo_formativo_id');
    }
}
