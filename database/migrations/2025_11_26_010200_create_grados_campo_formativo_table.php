<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados_campo_formativo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campo_formativo_id')->constrained('campos_formativos')->onDelete('cascade');
            $table->tinyInteger('grado');
            $table->integer('cantidad_alumnos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados_campo_formativo');
    }
};
