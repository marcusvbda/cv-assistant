<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;
use Mvbassalobre\GroqApiService\Services\GroqService;

Route::get('/', function () {
    // $service = new GroqService([]);
    // $service->user("hello, what is your name?")->ask();
    // dd($service->getThread());
    return redirect('/admin');
});

Route::get('/resume/{user}/preview', [PdfController::class, 'resumeStream'])->name('resume.stream');
Route::get('/download-pdf/{jobApplyDetail}/{type}', [PdfController::class, 'downloadPdf'])->name('download.pdf');
