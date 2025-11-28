<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormularioInformeRequest;
use App\Services\FormularioService;
use Illuminate\Http\JsonResponse;

class FormularioInformeController extends Controller
{
    protected $formularioService;

    public function __construct(FormularioService $formularioService)
    {
        $this->formularioService = $formularioService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormularioInformeRequest $request): JsonResponse
    { 
        dd($request->all());    
        try {
            $formulario = $this->formularioService->guardarFormulario($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Formulario guardado correctamente',
                'data' => $formulario
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el formulario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
