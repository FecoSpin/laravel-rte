<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulario_informes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('reporte_id')->constrained('rte_reports');
            $table->boolean('participa_en_proyectos')->default(false);
            $table->boolean('se_inscribio_en_cursos')->default(false);
            $table->integer('cantidad_cursos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulario_informes');
    }
};
