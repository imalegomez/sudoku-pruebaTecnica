<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('sudoku_games', function (Blueprint $table) {
        $table->id();
        $table->json('board'); // El tablero actual del juego (como JSON)
        $table->json('solution'); // La solución del juego (como JSON)
        $table->enum('status', ['in-progress', 'completed'])->default('in-progress');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sudoku_games');
    }
};
