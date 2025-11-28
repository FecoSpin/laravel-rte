<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioInforme extends Model
{
    use HasFactory;

    protected $table = 'formulario_informes';

    protected $fillable = [
        'usuario_id',
        'reporte_id',
        'participa_en_proyectos',
        'se_inscribio_en_cursos',
        'cantidad_cursos',
    ];

    protected $casts = [
        'participa_en_proyectos' => 'boolean',
        'se_inscribio_en_cursos' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function reporte()
    {
        return $this->belongsTo(RteReport::class, 'reporte_id');
    }

    public function camposFormativos()
    {
        return $this->hasMany(CampoFormativo::class, 'formulario_id');
    }

    public function proyectosColaborativos()
    {
        return $this->hasMany(ProyectoColaborativo::class, 'formulario_id');
    }

    public function cursosEnLinea()
    {
        return $this->hasMany(CursoEnLinea::class, 'formulario_id');
    }
    
}

