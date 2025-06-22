<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/resume/{user}/preview', [PdfController::class, 'resumeStream'])->name('resume.stream');
Route::get('/cover-letter/{user}/preview', [PdfController::class, 'coverLetterStream'])->name('cover-letter.stream');
