<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FormularioInforme;

class PdfFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'formulario_informe_id',
        'filename',
        'path',
        'url',
        'mime_type',
        'size'
    ];

    public function formularioInforme()
    {
        return $this->belongsTo(FormularioInforme::class);
    }
}
