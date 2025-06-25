<?php

namespace App\Filament\Widgets;

use App\Services\AIService;
use Filament\Widgets\Widget;
use Auth;
use Filament\Widgets\Concerns\CanPoll;

class ProfileAnalysisWidget extends Widget
{
    use CanPoll;

    protected static string $view = 'filament.widgets.profile-analysis';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;
    public bool $readyToLoad = false;

    public function loadWidget()
    {
        $this->readyToLoad = true;
    }

    protected function getViewData(): array
    {
        if (! $this->readyToLoad) return [];
        return $this->getData();
    }

    public function getData(): array
    {
        $user = Auth::user();
        $addresses = $user->addresses()->select('city', 'location')->get();
        $phones = $user->phones()->select('type', 'number')->get();
        $links = $user->links()->select('name', 'value')->get();
        $skills = $user->skills()->select('type', 'value')->get();
        $courses = $user->courses()->select(
            'instituition',
            'name',
            'start_date',
            'end_date',
        )->get();

        $experiences = $user->experiences()->select(
            'position',
            'description',
            'company',
            'start_date',
            'end_date',
            'start_date',
        )->get();

        $projects = $user->projects()->select(
            'name',
            'description',
            'start_date',
            'end_date',
            'start_date',
        )->get();

        $certificates = $user->certificates()->select(
            'name',
            'description',
            'date',
        )->get();

        $sections = [
            'addresses' => $addresses->count(),
            'position' => $user->position ? 1 : 0,
            'linkedin' => $user->linkedin ? 1 : 0,
            'email' => $user->email ? 1 : 0,
            'contacts' => $phones->count(),
            'links' => $links->count(),
            'skills' => $skills->count(),
            'courses' => $courses->count(),
            'experiences' => $experiences->count(),
            'projects' => $projects->count(),
            'certificates' => $certificates->count(),
        ];
        $filled = collect($sections)->filter(fn($count) => $count > 0)->count();
        $total = count($sections);

        $percentage = intval(($filled / $total) * 100);
        $dataSource = [
            "name" => $user->name,
            "introduction" => $user->introduction,
            "linkedin" => $user->linkedin,
            "position" => $user->position,
            "email" => $user->email,
            "addresses" => $addresses,
            "contacts" => $phones,
            "links" => $links,
            "skills" => $skills,
            "courses" => $courses,
            "experiences" => $experiences,
            "projects" => $projects,
            "certificates" => $certificates
        ];

        $service = AIService::make()->user('Analyze the data in the note and provide score (0-100) and comment/suggestions (max 200 characters) for generating a CV otimized to ATS.')
            ->user(json_encode($dataSource))
            ->user("Also let me know about any grammar mistakes and other inconsistencies.");
        $aiScore = $service->json(["comment" => "...", "score" => "..."])->generate();

        $score = data_get($aiScore, "score", 0);
        $scorePercentage = intval(($score / 100) * 100);

        return [
            'percentage' => $percentage,
            'feedback' => data_get($aiScore, "comment", "no comment"),
            'scorePercentage' => $scorePercentage,
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->hasAiIntegration();
    }
}
