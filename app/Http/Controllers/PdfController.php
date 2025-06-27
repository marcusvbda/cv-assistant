<?php

namespace App\Http\Controllers;

use App\Models\JobApplyDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use League\CommonMark\CommonMarkConverter;

class PdfController extends Controller
{
    public function resumeStream(User $user)
    {
        $pdf = Pdf::loadView('pdf.resume-templates.default', ['user' => $user])
            ->setPaper('a4');
        return $pdf->stream('preview.pdf');
    }

    public function downloadPdf(JobApplyDetail $jobApplyDetail, $type)
    {
        if (!$jobApplyDetail->{$type}) abort(404);
        $mdContent = $jobApplyDetail->{$type};

        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = $converter->convert($mdContent)->getContent();
        $renderedHtml = view('pdf.markdown-wrapper', ['html' => $html])->render();

        $pdf = Pdf::loadHTML($renderedHtml)->setPaper('a4');
        $user = Auth::user();
        $options = [
            "cover_letter" => "Cover Letter",
            "resume" => "Resume - [CV]",
        ];
        $fileName = $user->name . " (" . $options[$type] . ")";
        return $pdf->download("$fileName.pdf");
    }
}
