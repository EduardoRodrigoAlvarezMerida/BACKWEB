<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id('IdCliente');
            $table->string('Nombre');
            $table->string('Apellido');
            $table->string('Correo');
            $table->string('Contraseña');
            $table->string('Nit');
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        //eliminar migracion
        Schema::dropIfExists('clientes');
    }
    
};
