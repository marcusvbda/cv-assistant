<?php

use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;

Route::get('/resume/{user}/preview', [ResumeController::class, 'stream'])->name('resume.stream');
