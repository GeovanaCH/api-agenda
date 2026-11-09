<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->dateTime('data_hora');
            $table->string('status', 20);
            $table->string('assunto', 200);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes('excluido_em');

            $table->index(['cliente_id', 'data_hora']);
            $table->index('status');
            $table->index('data_hora');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
