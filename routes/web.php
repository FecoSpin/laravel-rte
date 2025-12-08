<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para la generación de PDFs
Route::get('/formulario/{id}/pdf', [PDFController::class, 'generarPDF'])
    ->name('formulario.pdf');

Route::get('/formulario/{id}/vista-previa', [PDFController::class, 'vistaPreviaPDF'])
    ->name('formulario.vista-previa');
