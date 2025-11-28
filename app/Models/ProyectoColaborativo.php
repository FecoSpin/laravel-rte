<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoColaborativo extends Model
{
    use HasFactory;

    protected $table = 'proyectos_colaborativos';

    protected $fillable = [
        'formulario_id',
        'nombre'
    ];

    public function formulario()
    {
        return $this->belongsTo(FormularioInforme::class, 'formulario_id');
    }
}
