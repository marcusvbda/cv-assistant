<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/resume/{user}/preview', [PdfController::class, 'resumeStream'])->name('resume.stream');
Route::get('/download-pdf/{jobApplyDetail}/{type}', [PdfController::class, 'downloadPdf'])->name('download.pdf');
