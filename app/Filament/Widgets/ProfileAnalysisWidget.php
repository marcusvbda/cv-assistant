<?php

namespace App\Filament\Widgets;

use Cache;
use Filament\Widgets\Widget;
use Auth;
use Filament\Widgets\Concerns\CanPoll;
use marcusvbda\GroqApiService\Services\GroqService;

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
        $payload = $user->getPayloadContext();
        return Cache::rememberForever(md5(json_encode($payload)), function () use ($payload) {
            $filled = collect($payload)->filter(fn($count) => $count > 0)->count();
            $total = count($payload);
            $percentage = intval(($filled / $total) * 100);

            $service = new GroqService([
                "thread" => [
                    [
                        "role" => "system",
                        "content" => "Analyze this data " . json_encode($payload) . " data in the note and provide score (0-100) and comment/suggestions (max 200 characters) for generating a CV otimized to ATS."
                    ],
                    [
                        "role" => "user",
                        "content" => "Also let me know about any grammar mistakes and other inconsistencies."
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

            return [
                'percentage' => $percentage,
                'feedback' => data_get($aiScore, "comment", "no comment"),
                'scorePercentage' => $scorePercentage,
            ];
        });
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->hasAiIntegration();
    }
}
