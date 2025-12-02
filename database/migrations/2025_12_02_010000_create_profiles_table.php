<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('work_center_name')->nullable(); // NOMBRE DEL CENTRO DE TRABAJO
            $table->string('cct')->nullable();              // CCT
            $table->string('zone')->nullable();             // ZONA

            $table->string('rte_name')->nullable();         // NOMBRE DEL RTE
            $table->string('shift')->nullable();            // TURNO
            $table->string('sector')->nullable();           // SECTOR

            $table->string('report_period')->nullable();    // PERIODO A REPORTAR
            $table->unsignedInteger('commissioned_hours')->nullable(); // HORAS COMISIONADAS

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
