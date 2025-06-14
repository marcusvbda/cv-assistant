<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;
use Auth;

class AIService
{
    private $messages;
    private $provider;
    private $url;
    private $key;

    public function __construct($messages)
    {
        $user = Auth::user();
        $ai_integration = $user->ai_integration ?? [];
        $this->key = data_get($ai_integration, "key");
        $this->provider = data_get($ai_integration, "provider");
        $this->messages = $messages;
        $this->model = data_get(["groq" => "meta-llama/llama-4-scout-17b-16e-instruct"], $this->provider);
        $this->url = data_get([
            "groq" => "https://api.groq.com/openai/v1"
        ], $this->provider);
    }

    public static function make($messages = []): self
    {
        return new self($messages);
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

        try {
            config(['openai.api_key' => $this->key]);
            config(['openai.base_uri' => $this->url]);

            return Cache::rememberForever($cacheKey, function () {
                $response = OpenAI::chat()->create([
                    'model' => $this->model,
                    'messages' => $this->messages,
                ]);

                return data_get($response, "choices.0.message.content", '');
            });
        } catch (\Throwable $e) {
            return 'invalid AI settings';
        }
    }


    private function getCacheKey(): string
    {
        $payload = [
            'provider' => $this->provider,
            'key' => $this->key,
            'model' => $this->model,
            'messages' => $this->messages,
        ];

        return 'ai_response_' . md5(json_encode($payload));
    }
}
