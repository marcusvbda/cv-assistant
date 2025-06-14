<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;

class AIService
{
    private $messages;
    private $model;

    public function __construct($model, $messages)
    {
        $this->messages = $messages;
        $this->model = $model;
    }

    public static function make($model = "meta-llama/llama-4-scout-17b-16e-instruct", $messages = []): self
    {
        return new self($model, $messages);
    }

    public function model($value): self
    {
        $this->model = $value;
        return $this;
    }

    public function addMessage($role, $value): self
    {
        $this->messages[] = ["role" => $role, "content" => $value];
        return $this;
    }

    public function system($value): self
    {
        $this->addMessage("system", $value);
        return $this;
    }

    public function user($value): self
    {
        $this->addMessage("user", $value);
        return $this;
    }

    public function generate()
    {
        $cacheKey = $this->getCacheKey();
        return Cache::rememberForever($cacheKey, function () {
            $response = OpenAI::chat()->create([
                'model' => $this->model,
                'messages' => $this->messages,
            ]);

            return data_get($response, "choices.0.message.content", '');
        });
    }

    private function getCacheKey(): string
    {
        $payload = [
            'model' => $this->model,
            'messages' => $this->messages,
        ];

        return 'ai_response_' . md5(json_encode($payload));
    }
}
