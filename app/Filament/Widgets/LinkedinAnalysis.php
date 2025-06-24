<?php

namespace App\Filament\Widgets;

use App\Services\AIService;
use Filament\Widgets\Widget;
use Auth;
use Filament\Widgets\Concerns\CanPoll;

class LinkedinAnalysis extends Widget
{
    use CanPoll;

    protected static string $view = 'filament.widgets.linkedin-analysis';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    public bool $readyToLoad = false;

    public function getCompletionData(): array
    {
        $user = Auth::user();
        $linkedin = $user->linkedin;
        if (!$linkedin) {
            $aiScore = [
                "comment" => "no linkedin profile",
                "score" => 0
            ];
        } else {
            $service = AIService::make()->user('Analyze the linkedin profile (url provided) in the note and provide score (0-100) and comment/suggestions (max 200 characters).')
                ->user($user->linkedin)
                ->user("Also let me know about any grammar mistakes and other inconsistencies.");
            $aiScore = $service->json(["comment" => "...", "score" => "..."])->generate();

            $score = data_get($aiScore, "score", 0);
            $scorePercentage = intval(($score / 100) * 100);
        }

        return [
            'feedback' => data_get($aiScore, "comment", "no comment"),
            'score' => $score ?? 0,
            'scorePercentage' => $scorePercentage ?? 0,
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->hasAiIntegration();
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
