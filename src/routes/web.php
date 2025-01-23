<?php

use Illuminate\Support\Facades\Route;
use Developervkindia\ModuleGenerator\Controllers\ModuleGeneratorController;

Route::get('test', ModuleGeneratorController::class);
Route::get('/module-generator', function () {
    return view('views::module-generator.form');
})->name('module-generator.form');
Route::post('/module-generator/generate', [ModuleGeneratorController::class, 'generate'])
    ->name('module-generator.generate');
