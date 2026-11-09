<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120);
            $table->string('email', 150);
            $table->string('cpf', 11);
            $table->string('telefone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes('excluido_em');

            $table->index('nome');
            $table->index('email');
            $table->index('cpf');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
