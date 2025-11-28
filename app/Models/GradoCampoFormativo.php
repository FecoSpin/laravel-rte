<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoCampoFormativo extends Model
{
    use HasFactory;

    protected $table = 'grados_campo_formativo';

    protected $fillable = [
        'campo_formativo_id',
        'grado',
        'cantidad_alumnos'
    ];

    protected $casts = [
        'grado' => 'integer',
        'cantidad_alumnos' => 'integer',
    ];

    public function campoFormativo()
    {
        return $this->belongsTo(CampoFormativo::class, 'campo_formativo_id');
    }
}
