# 📚 API Documentation - Sistema RTE (Revisión Técnica Especializada)

## 🔐 Autenticación

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "admin@rte.com",
    "password": "password"
}
```

**Respuesta:**
```json
{
    "user": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@rte.com",
        "role": "admin",
        "zone": null
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
}
```

### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Obtener Usuario Actual
```http
GET /api/auth/user
Authorization: Bearer {token}
```

## 👥 Usuarios

### Listar Usuarios (Solo Admin)
```http
GET /api/users?role=technician&zone_id=1&search=juan
Authorization: Bearer {token}
```

### Crear Usuario (Solo Admin)
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Juan Pérez",
    "email": "juan@rte.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "technician",
    "zone_id": 1,
    "active": true
}
```

### Actualizar Usuario
```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Juan Pérez Actualizado",
    "email": "juan.updated@rte.com",
    "role": "supervisor",
    "zone_id": 2,
    "active": true
}
```

## 🗺️ Zonas

### Listar Zonas
```http
GET /api/zones?active=true&search=norte
Authorization: Bearer {token}
```

### Crear Zona (Admin/Supervisor)
```http
POST /api/zones
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Zona Norte",
    "code": "ZN001",
    "description": "Zona que comprende las escuelas del norte",
    "active": true
}
```

### Activar/Desactivar Zona
```http
POST /api/zones/{id}/toggle
Authorization: Bearer {token}
```

## 📋 Encuestas RTE

### Listar Encuestas
```http
GET /api/surveys?status=submitted&zone_id=1&stage=initial
Authorization: Bearer {token}
```

### Crear Encuesta
```http
POST /api/surveys
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "RTE Escuela Primaria",
    "description": "Revisión técnica de infraestructura",
    "zone_id": 1,
    "form_data": {
        "infrastructure": "Estado general bueno",
        "equipment": "Equipos funcionando correctamente",
        "safety": "Medidas de seguridad implementadas"
    }
}
```

### Enviar Encuesta para Revisión
```http
POST /api/surveys/{id}/submit
Authorization: Bearer {token}
```

### Aprobar Encuesta (Admin/Supervisor)
```http
POST /api/surveys/{id}/approve
Authorization: Bearer {token}
Content-Type: application/json

{
    "stage": "first_report"
}
```

### Rechazar Encuesta (Admin/Supervisor)
```http
POST /api/surveys/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
    "rejection_reason": "Información incompleta en la sección de equipos"
}
```

## 📄 Reportes RTE

### Listar Reportes
```http
GET /api/reports?status=approved&report_type=first&survey_id=1
Authorization: Bearer {token}
```

### Crear Reporte
```http
POST /api/reports
Authorization: Bearer {token}
Content-Type: application/json

{
    "survey_id": 1,
    "report_type": "first",
    "title": "Primer Reporte RTE",
    "content": {
        "findings": "Hallazgos principales...",
        "recommendations": "Recomendaciones...",
        "conclusions": "Conclusiones..."
    }
}
```

### Generar PDF del Reporte
```http
GET /api/reports/{id}/pdf
Authorization: Bearer {token}
```

## 🔧 Solicitudes de Mantenimiento

### Listar Solicitudes
```http
GET /api/maintenance-requests?status=pending&priority=high&zone_id=1
Authorization: Bearer {token}
```

### Crear Solicitud
```http
POST /api/maintenance-requests
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Reparación de ventanas",
    "description": "Las ventanas del aula 3 necesitan reparación urgente",
    "priority": "high",
    "zone_id": 1,
    "location": "Aula 3, Planta Baja",
    "images": ["base64_image_1", "base64_image_2"]
}
```

### Asignar Solicitud (Admin/Supervisor)
```http
POST /api/maintenance-requests/{id}/assign
Authorization: Bearer {token}
Content-Type: application/json

{
    "assigned_to": 5
}
```

### Completar Solicitud
```http
POST /api/maintenance-requests/{id}/complete
Authorization: Bearer {token}
Content-Type: application/json

{
    "resolution_notes": "Reparación completada exitosamente",
    "evidence_images": ["base64_evidence_1"]
}
```

## 📎 Archivos Adjuntos

### Subir Archivo
```http
POST /api/attachments
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "attachable_type": "App\\Models\\RteSurvey",
    "attachable_id": 1,
    "file": [archivo],
    "type": "image"
}
```

### Subir Múltiples Archivos
```http
POST /api/attachments/bulk-upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "attachable_type": "App\\Models\\MaintenanceRequest",
    "attachable_id": 1,
    "files": [archivo1, archivo2, archivo3]
}
```

### Descargar Archivo
```http
GET /api/attachments/{id}/download
Authorization: Bearer {token}
```

## 📊 Dashboard y Estadísticas

### Obtener Estadísticas
```http
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
    "surveys": {
        "total": 150,
        "draft": 20,
        "submitted": 30,
        "approved": 90,
        "my_surveys": 15
    },
    "reports": {
        "total": 80,
        "draft": 10,
        "submitted": 15,
        "approved": 55
    },
    "maintenance_requests": {
        "total": 200,
        "pending": 45,
        "in_progress": 30,
        "completed": 120,
        "assigned_to_me": 8
    }
}
```

## 🔒 Roles y Permisos

### Roles Disponibles:
- **admin**: Acceso completo al sistema
- **supervisor**: Gestión de su zona asignada
- **technician**: Creación y gestión de sus propias encuestas/reportes
- **user**: Acceso básico para crear solicitudes

### Permisos por Rol:

| Acción | Admin | Supervisor | Technician | User |
|--------|-------|------------|------------|------|
| Gestionar usuarios | ✅ | ❌ | ❌ | ❌ |
| Gestionar zonas | ✅ | ✅ | ❌ | ❌ |
| Aprobar encuestas | ✅ | ✅ (su zona) | ❌ | ❌ |
| Crear encuestas | ✅ | ✅ | ✅ | ❌ |
| Asignar mantenimiento | ✅ | ✅ (su zona) | ❌ | ❌ |
| Crear solicitudes | ✅ | ✅ | ✅ | ✅ |

## 🚀 Códigos de Estado HTTP

- **200**: OK - Solicitud exitosa
- **201**: Created - Recurso creado exitosamente
- **401**: Unauthorized - Token inválido o faltante
- **403**: Forbidden - Sin permisos para la acción
- **404**: Not Found - Recurso no encontrado
- **422**: Unprocessable Entity - Errores de validación
- **500**: Internal Server Error - Error del servidor

## 📝 Ejemplos de Errores

### Error de Validación
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Error de Autorización
```json
{
    "message": "No tienes permisos para acceder a este recurso"
}
```

### Error de Autenticación
```json
{
    "message": "Unauthenticated."
}
```
