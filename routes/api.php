<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\RteSurveyController;
use App\Http\Controllers\Api\RteReportController;
use App\Http\Controllers\Api\MaintenanceRequestController;
use App\Http\Controllers\Api\ReportAttachmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportFileController;
use App\Http\Controllers\FormularioInformeController;
use App\Http\Controllers\InformeController;
use App\Http\Middleware\Cors;


// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/users-test', [UserController::class, 'index2']);

Route::get('/test-cors', function () {
    return response()->json(['ok' => true]);
});


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/test-cors', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'ok-cors' => true,
            'method' => $request->method(),
        ]);
    });
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);

    // User profile routes
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::put('/profile/password', function (Request $request) {
        return app(UserController::class)->updatePassword($request, $request->user());
    });

    // Zones - Admin and Supervisor access
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::apiResource('zones', ZoneController::class);
        Route::post('zones/{zone}/toggle', [ZoneController::class, 'toggle']);
    });

    // RTE Surveys
    Route::apiResource('surveys', RteSurveyController::class);
    Route::post('surveys/{survey}/submit', [RteSurveyController::class, 'submit']);
    
    // Survey approval routes - Admin and Supervisor only
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('surveys/{survey}/approve', [RteSurveyController::class, 'approve']);
        Route::post('surveys/{survey}/reject', [RteSurveyController::class, 'reject']);
    });

    // RTE Reports
    Route::apiResource('reports', RteReportController::class);
    
    // Formulario de Informe
    Route::post('/formulario-informe', [InformeController::class, 'store'])->middleware('auth:sanctum');
    
    // Subida de archivos de reportes (PDF / imágenes) por usuario y tipo de reporte
    Route::post('/reportes/{report_type}/upload', [ReportFileController::class, 'store']);
    Route::post('reports/{report}/submit', [RteReportController::class, 'submit']);
    Route::get('reports/{report}/pdf', [RteReportController::class, 'generatePdf']);
    
    // Report approval routes - Admin and Supervisor only
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('reports/{report}/approve', [RteReportController::class, 'approve']);
        Route::post('reports/{report}/reject', [RteReportController::class, 'reject']);
    });

    // Maintenance Requests
    Route::apiResource('maintenance-requests', MaintenanceRequestController::class);
    
    // Maintenance request management - Admin and Supervisor only
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('maintenance-requests/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assign']);
        Route::post('maintenance-requests/{maintenanceRequest}/reject', [MaintenanceRequestController::class, 'reject']);
    });
    
    // Complete maintenance request - Assigned technician or admin/supervisor
    Route::post('maintenance-requests/{maintenanceRequest}/complete', [MaintenanceRequestController::class, 'complete']);

    // File attachments
    Route::apiResource('attachments', ReportAttachmentController::class)->except(['update']);
    Route::get('attachments/{attachment}/download', [ReportAttachmentController::class, 'download']);
    Route::post('attachments/bulk-upload', [ReportAttachmentController::class, 'bulkUpload']);

    // User management - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/toggle', [UserController::class, 'toggle']);
        Route::put('users/{user}/password', [UserController::class, 'updatePassword']);
    });

    // Dashboard and statistics routes
    Route::get('/dashboard/stats', function (Request $request) {
        $user = $request->user();
        
        $stats = [
            'surveys' => [
                'total' => \App\Models\RteSurvey::count(),
                'draft' => \App\Models\RteSurvey::where('status', 'draft')->count(),
                'submitted' => \App\Models\RteSurvey::where('status', 'submitted')->count(),
                'approved' => \App\Models\RteSurvey::where('status', 'approved')->count(),
            ],
            'reports' => [
                'total' => \App\Models\RteReport::count(),
                'draft' => \App\Models\RteReport::where('status', 'draft')->count(),
                'submitted' => \App\Models\RteReport::where('status', 'submitted')->count(),
                'approved' => \App\Models\RteReport::where('status', 'approved')->count(),
            ],
            'maintenance_requests' => [
                'total' => \App\Models\MaintenanceRequest::count(),
                'pending' => \App\Models\MaintenanceRequest::where('status', 'pending')->count(),
                'in_progress' => \App\Models\MaintenanceRequest::where('status', 'in_progress')->count(),
                'completed' => \App\Models\MaintenanceRequest::where('status', 'completed')->count(),
            ],
        ];

        // Filter by user role and zone
        if ($user->role === 'technician') {
            $stats['surveys']['my_surveys'] = \App\Models\RteSurvey::where('user_id', $user->id)->count();
            $stats['maintenance_requests']['my_requests'] = \App\Models\MaintenanceRequest::where('requested_by', $user->id)->count();
            $stats['maintenance_requests']['assigned_to_me'] = \App\Models\MaintenanceRequest::where('assigned_to', $user->id)->count();
        } elseif ($user->role === 'supervisor' && $user->zone_id) {
            $stats['surveys']['zone_surveys'] = \App\Models\RteSurvey::where('zone_id', $user->zone_id)->count();
            $stats['maintenance_requests']['zone_requests'] = \App\Models\MaintenanceRequest::where('zone_id', $user->zone_id)->count();
        }

        return response()->json($stats);
    });
});
