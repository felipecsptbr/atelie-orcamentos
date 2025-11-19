<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrcamentoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [OrcamentoController::class, 'index'])->name('home');

Route::resource('orcamentos', OrcamentoController::class);

Route::get('orcamentos/{orcamento}/pdf', [OrcamentoController::class, 'pdf'])
    ->name('orcamentos.pdf');