<?php

namespace App\Http\Controllers;

use App\Models\FormularioInforme;
use App\Models\PdfFile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PDFController extends Controller
{
    /**
     * Genera un PDF con la información del formulario y devuelve la URL para su descarga
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function generarPDF($id)
    {
        try {
            // Obtener el formulario con sus relaciones
            $formulario = FormularioInforme::with([
                'usuario.profile',
                'reporte',
                'camposFormativos.grados',
                'proyectosColaborativos',
                'cursosEnLinea'
            ])->findOrFail($id);

            // Transformar los datos para la vista
            $formulario->camposFormativos->each(function($campo) {
                // Inicializar contadores para cada grado
                $grados = [
                    1 => 0, 2 => 0, 3 => 0, 
                    4 => 0, 5 => 0, 6 => 0
                ];

                // Llenar con los valores reales
                foreach ($campo->grados as $grado) {
                    $grados[$grado->grado] = $grado->cantidad_alumnos;
                }

                // Agregar propiedades dinámicas al modelo
                $campo->setAttribute('alumnos_por_grado', $grados);
            });

            $profile = optional($formulario->usuario)->profile;

            // Generar el PDF
            $pdf = PDF::loadView('pdf.informe', [
                'formulario' => $formulario,
                'profile' => $profile,
                'fecha' => now()->format('d/m/Y H:i:s')
            ]);

            // Configuración del PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            // Generar un nombre de archivo único
            $filename = 'informe-formulario-' . $formulario->id . '-' . now()->format('YmdHis') . '.pdf';
            $path = 'pdfs/' . $filename;
            
            // Guardar el PDF en el almacenamiento
            Storage::disk('public')->put($path, $pdf->output());
            
            // Obtener la URL pública del archivo
            $url = Storage::disk('public')->url($path);
            
            // Guardar la información del PDF en la base de datos
            $pdfFile = PdfFile::create([
                'formulario_informe_id' => $formulario->id,
                'filename' => $filename,
                'path' => $path,
                'url' => $url,
                'mime_type' => 'application/pdf',
                'size' => Storage::disk('public')->size($path)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF generado exitosamente',
                'data' => [
                    'pdf_url' => $url,
                    'download_url' => route('pdf.download', ['id' => $pdfFile->id]),
                    'created_at' => now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una vista previa del PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function vistaPreviaPDF($id)
    {
        // Obtener el formulario con sus relaciones
        $formulario = FormularioInforme::with([
            'usuario',
            'reporte',
            'camposFormativos',
            'proyectosColaborativos',
            'cursosEnLinea'
        ])->findOrFail($id);
        
         // Transformar los datos para la vista
        $formulario->camposFormativos->each(function($campo) {
            // Inicializar contadores para cada grado
            $grados = [
                1 => 0, 2 => 0, 3 => 0, 
                4 => 0, 5 => 0, 6 => 0
            ];

            // Llenar con los valores reales
            foreach ($campo->grados as $grado) {
                $grados[$grado->grado] = $grado->cantidad_alumnos;
            }

            // Agregar propiedades dinámicas al modelo
            $campo->setAttribute('alumnos_por_grado', $grados);
        });
        return view('pdf.informe', [
            'formulario' => $formulario,
            'fecha' => now()->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Genera y descarga el PDF del primer informe a partir del ID del formulario
     *
     * @param  int  $id  ID del formulario_informes
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function downloadPDF($id)
    {
        // Cargar el formulario con todas las relaciones necesarias
        $formulario = FormularioInforme::with([
            'usuario.profile',
            'reporte',
            'camposFormativos',
            'proyectosColaborativos',
            'cursosEnLinea'
        ])->findOrFail($id);

        $profile = optional($formulario->usuario)->profile;

        // Generar el PDF usando la misma vista del informe
        $pdf = Pdf::loadView('pdf.informe', [
            'formulario' => $formulario,
            'profile' => $profile,
            'fecha' => now()->format('d/m/Y H:i:s'),
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'informe-formulario-' . $formulario->id . '.pdf';

        return $pdf->download($filename);
    }
    
    /**
     * Obtiene la lista de PDFs generados para un formulario
     *
     * @param  int  $formularioId
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPDFs($formularioId)
    {
        $formulario = FormularioInforme::with('pdfFiles')->findOrFail($formularioId);
        
        return response()->json([
            'success' => true,
            'data' => $formulario->pdfFiles->map(function($file) {
                return [
                    'id' => $file->id,
                    'filename' => $file->filename,
                    'url' => $file->url,
                    'download_url' => route('pdf.download', ['id' => $file->id]),
                    'created_at' => $file->created_at->toDateTimeString(),
                    'size' => $file->size,
                    'size_formatted' => $this->formatBytes($file->size)
                ];
            })
        ]);
    }
    
    /**
     * Formatea el tamaño del archivo en un formato legible
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
