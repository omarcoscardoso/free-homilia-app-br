<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiHomiliaController;

Route::get('/', fn() => view('home'))->name("gemini-homilia.create");
Route::post('/export-pdf', [GeminiHomiliaController::class, 'exportPdf'])->name('gemini-homilia.export-pdf');

