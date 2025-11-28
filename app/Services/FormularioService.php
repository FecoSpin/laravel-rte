<?php

namespace App\Services;

use App\Models\FormularioInforme;
use App\Models\CampoFormativo;
use App\Models\GradoCampoFormativo;
use App\Models\ProyectoColaborativo;
use App\Models\CursoEnLinea;
use App\Models\RteReport;
use Illuminate\Support\Facades\DB;

class FormularioService
{
    public function guardarFormulario(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Crear el reporte primero
            $reporte = RteReport::create([
                'survey_id' => $data['survey_id'] ?? null,
                'report_type' => 'first', // o el tipo de informe que corresponda
                'title' => 'Informe de Actividades',
                'content' => [],
                'status' => 'draft',
                'version' => 1,
            ]);

            // Crear el formulario
            $formulario = FormularioInforme::create([
                'usuario_id' => $data['usuarioId'],
                'reporte_id' => $reporte->id,
                'participa_en_proyectos' => $data['participaEnProyectos'],
                'se_inscribio_en_cursos' => $data['seInscribioEnCursos'],
                'cantidad_cursos' => $data['cantidadCursos'] ?? 0,
            ]);

            // Guardar campos formativos y sus grados
            foreach ($data['camposFormativos'] as $campoData) {
                $campo = $formulario->camposFormativos()->create([
                    'nombre' => $campoData['nombre']
                ]);

                foreach ($campoData['grados'] as $gradoData) {
                    $campo->grados()->create([
                        'grado' => $gradoData['grado'],
                        'cantidad_alumnos' => $gradoData['cantidadAlumnos']
                    ]);
                }
            }

            // Guardar proyectos colaborativos si aplica
            if ($data['participaEnProyectos'] && !empty($data['proyectosColaborativos'])) {
                foreach ($data['proyectosColaborativos'] as $proyecto) {
                    $formulario->proyectosColaborativos()->create([
                        'nombre' => $proyecto
                    ]);
                }
            }

            // Guardar cursos en línea si aplica
            if ($data['seInscribioEnCursos'] && !empty($data['cursosEnLinea'])) {
                foreach ($data['cursosEnLinea'] as $curso) {
                    $formulario->cursosEnLinea()->create([
                        'nombre' => $curso
                    ]);
                }
            }

            return $formulario->load(['camposFormativos.grados', 'proyectosColaborativos', 'cursosEnLinea']);
        });
    }
}
