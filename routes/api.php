<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SudokuController;

Route::post('/sudoku/games', [SudokuController::class, 'createGame']); // Crear un nuevo juego
Route::get('/sudoku/games/{id}', [SudokuController::class, 'getGame']); // Obtener un juego por ID
Route::post('/sudoku/games/{id}/validate', [SudokuController::class, 'validateMove']); // Validar un movimiento

