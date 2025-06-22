<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function resumeStream(User $user)
    {
        $pdf = Pdf::loadView('resume-templates.default', ['user' => $user])
            ->setPaper('a4');
        return $pdf->stream('preview.pdf');
    }

    public function coverLetterStream(User $user)
    {
        $pdf = Pdf::loadView('cover-letter-templates.default', ['user' => $user])
            ->setPaper('a4');
        return $pdf->stream('cover-letter.pdf');
    }
}
