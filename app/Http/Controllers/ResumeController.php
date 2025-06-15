<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeController extends Controller
{
    public function stream(User $user)
    {
        $pdf = Pdf::loadView('resume-templates.default', ['user' => $user])
            ->setPaper('a4');
        return $pdf->stream('preview.pdf');
    }
}
