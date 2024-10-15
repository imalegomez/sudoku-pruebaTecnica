<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SudokuGame; // Importar el modelo SudokuGame

class SudokuController extends Controller
{
    public function createGame()
    {
        // Generar el tablero y la solución
        $board = $this->generateBoard(); // Genera un tablero de Sudoku con algunas celdas vacías
        $solution = $this->generateSolution(); // La solución completa del Sudoku

        // Crear el juego en la base de datos
        $game = SudokuGame::create([
            'board' => json_encode($board),
            'solution' => json_encode($solution),
            'status' => 'in-progress',
        ]);

        return response()->json(['game' => $game], 201);
    }

    public function getGame($id)
    {
        $game = SudokuGame::findOrFail($id);
        return response()->json($game);
    }

    public function validateMove(Request $request, $id)
    {
        $request->validate([
            'row' => 'required|integer|between:0,8',
            'col' => 'required|integer|between:0,8',
            'value' => 'required|integer|between:1,9',
        ]);

        $game = SudokuGame::findOrFail($id);
        $solution = json_decode($game->solution, true);

        $row = $request->input('row');
        $col = $request->input('col');
        $value = $request->input('value');

        if ($solution[$row][$col] == $value) {
            return response()->json(['valid' => true]);
        } else {
            return response()->json(['valid' => false]);
        }
    }

    private function generateBoard()
    {
        $board = $this->generateSolution();
        $this->removeNumbers($board);

        return $board;
    }

    private function generateSolution()
    {
        $board = array_fill(0, 9, array_fill(0, 9, 0));

        if ($this->solveSudoku($board)) {
            return $board;
        }

        return null;
    }

    private function solveSudoku(&$board)
    {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($board[$row][$col] == 0) {
                    $numbers = range(1,9);
                    shuffle($numbers);
                    foreach ($numbers as $num) {
                        if ($this->isSafe($board, $row, $col, $num)) {
                            $board[$row][$col] = $num;
                            if ($this->solveSudoku($board)) {
                                return true;
                            }
                            $board[$row][$col] = 0;
                        }
                    }
                    return false;
                }
            }
        }
        return true;
    }

    private function isSafe($board, $row, $col, $num)
    {
        // Comprueba la fila
        for ($x = 0; $x < 9; $x++) {
            if ($board[$row][$x] == $num) {
                return false;
            }
        }

        // Comprueba la columna
        for ($x = 0; $x < 9; $x++) {
            if ($board[$x][$col] == $num) {
                return false;
            }
        }

        // Comprueba el cuadrante 3x3
        $startRow = $row - $row % 3;
        $startCol = $col - $col % 3;
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($board[$i + $startRow][$j + $startCol] == $num) {
                    return false;
                }
            }
        }

        return true; // El número es seguro
    }

    private function removeNumbers(&$board)
    {
        $cellsToRemove = 40; // Ajusta este número para tener más o menos celdas vacías
        while ($cellsToRemove > 0) {
            $row = rand(0, 8);
            $col = rand(0, 8);
            if ($board[$row][$col] != 0) {
                $board[$row][$col] = 0; // Elimina el número
                $cellsToRemove--;
            }
        }
    }
}
