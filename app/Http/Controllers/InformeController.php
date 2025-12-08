<?php

namespace App\Http\Controllers;
use App\Models\FormularioInforme;
use App\Models\CampoFormativo;
use App\Models\GradoCampoFormativo;
use App\Models\ProyectoColaborativo;
use App\Models\CursoEnLinea;
use App\Models\RteReport;

use Illuminate\Http\Request;

class InformeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        try {
            // Lógica para guardar el informe
             // Crear el reporte primero
            $reporte = RteReport::create([
                'survey_id' => 1,
                'report_type' => 'first', // o el tipo de informe que corresponda
                'title' => 'Informe de Actividades',
                'content' => [],
                'status' => 'draft',
                'version' => 1,
            ]);

             // Crear el formulario
            $formulario = FormularioInforme::create([
                'usuario_id' => $request['usuarioId'],
                'reporte_id' =>$reporte->id,
                'participa_en_proyectos' => $request['participaEnProyectos'],
                'se_inscribio_en_cursos' => $request['seInscribioEnCursos'],
                'cantidad_cursos' => $request['cantidadCursos'] ?? 0,
            ]);

            // Guardar campos formativos y sus grados
            foreach ($request['camposFormativos'] as $campoData) {
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
            if ($request['participaEnProyectos'] && !empty($request['proyectosColaborativos'])) {
                foreach ($request['proyectosColaborativos'] as $proyecto) {
                    $formulario->proyectosColaborativos()->create([
                        'nombre' => $proyecto
                    ]);
                }
            }

            // Guardar cursos en línea si aplica
            if ($request['seInscribioEnCursos'] && !empty($request['cursosEnLinea'])) {
                foreach ($request['cursosEnLinea'] as $curso) {
                    $formulario->cursosEnLinea()->create([
                        'nombre' => $curso
                    ]);
                }
            }

            //$formulario->load(['camposFormativos.grados', 'proyectosColaborativos', 'cursosEnLinea']);
    


            return response()->json([
                'success' => true,
                'message' => 'Informe guardado correctamente',
                'data' => $formulario
                
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el informe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
