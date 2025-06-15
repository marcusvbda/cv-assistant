<?php

namespace App\Filament\Widgets;

use App\Services\AIService;
use Filament\Widgets\Widget;
use Auth;
use Filament\Widgets\Concerns\CanPoll;

class ProfileCompletion extends Widget
{
    use CanPoll;

    protected static string $view = 'filament.widgets.profile-completion';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;
    public bool $readyToLoad = false;

    public function getCompletionData(): array
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

        $service = AIService::make()->user('Analyze the data in the note and provide score (0-100) and comment (max 200 characters) for generating a CV.')
            ->user(json_encode($dataSource));
        $aiScore = $service->json(["comment" => "...", "score" => "..."])->generate();

        $score = data_get($aiScore, "score", 0);
        $scorePercentage = intval(($score / 100) * 100);

        return [
            'percentage' => $percentage ?? 0,
            'verdict' => data_get($aiScore, "comment", "no comment"),
            'score' => $score,
            'scorePercentage' => $scorePercentage
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        $ai_integration = $user->ai_integration ?? [];
        return boolval(data_get($ai_integration, "key"));
    }


    public function loadWidget()
    {
        $this->readyToLoad = true;
    }

    protected function getViewData(): array
    {
        if (! $this->readyToLoad) {
            return [];
        }

        return $this->getCompletionData();
    }
}
