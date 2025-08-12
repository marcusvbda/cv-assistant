<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Auth;
use Filament\Widgets\Concerns\CanPoll;
use marcusvbda\GroqApiService\Services\GroqService;
use Cache;

class LinkedinAnalysisWidget extends Widget
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
        return Cache::rememberForever($linkedin, function () use ($linkedin) {
            if (!$linkedin) {
                $aiScore = [
                    "comment" => "no linkedin profile",
                    "score" => 0
                ];
            } else {
                $service = new GroqService([
                    "thread" => [
                        [
                            "role" => "system",
                            "content" => "Analyze this LinkedIn profile ($linkedin) in the note and provide a score (0-100) and comment/suggestions (max 200 characters)."
                        ],
                        [
                            "role" => "user",
                            "content" => "Also check for any grammar mistakes and other inconsistencies."
                        ],
                        [
                            "role" => "user",
                            "content" => "Respond ONLY with a valid JSON object, no explanations, no extra text, no greetings, no ```json"
                        ],
                        [
                            "role" => "user",
                            "content" => "The JSON format must be exactly:\n{\n  \"comment\": \"(string) your comment here\",\n  \"score\": (integer between 0 and 100)\n}\nIf the URL is missing, respond with empty or default values, but still valid JSON."
                        ]
                    ]
                ])->ask();

                $lastMessage = $service->getLastMessage();
                $aiScore = json_decode(data_get($lastMessage, "content", "{}"));
                $score = data_get($aiScore, "score", 0);
                $scorePercentage = intval(($score / 100) * 100);
            }

            return [
                'feedback' => data_get($aiScore, "comment", "no comment"),
                'score' => $score ?? 0,
                'scorePercentage' => $scorePercentage ?? 0,
            ];
        });
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
