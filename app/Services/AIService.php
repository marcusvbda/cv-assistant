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
    private $isJson = false;
    private $jsonFormat;
    private $temperature = 0.7;

    public function __construct($messages)
    {
        $user = Auth::user();
        $ai_integration = $user->ai_integration ?? [];
        $this->key = data_get($ai_integration, "key");
        $this->setProvider(data_get($ai_integration, "provider"));
        $this->messages = $messages;
        $this->model = data_get(["groq" => "meta-llama/llama-4-scout-17b-16e-instruct"], $this->provider);
    }

    public function setProvider($provider): self
    {
        $this->url = data_get([
            "groq" => "https://api.groq.com/openai/v1"
        ], $provider);
        $this->provider = $provider;
        return $this;
    }

    public function setKey($key): self
    {
        $this->key = $key;
        return $this;
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

    public function temperature($value): self
    {
        $this->temperature = $value;
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

    public function json($format = null): self
    {
        $this->isJson = true;
        if ($format) $this->jsonFormat = $format;
        return $this;
    }

    public function generate(): mixed
    {
        $cacheKey = $this->getCacheKey();

        try {
            config(['openai.base_uri' => $this->url]);
            config(['openai.api_key' => $this->key]);

            return Cache::rememberForever($cacheKey, function () {
                $response = OpenAI::chat()->create($this->getOptions());
                $result = data_get($response, "choices.0.message.content", '');
                return $this->isJson ? json_decode($result, true) : $result;
            });
        } catch (\Throwable $e) {
            return 'invalid AI settings';
        }
    }

    private function getOptions(): array
    {
        $options = [
            'model' => $this->model,
            'messages' => $this->messages,
            'temperature' => $this->temperature
        ];
        if ($this->isJson) {
            $options["response_format"] = ["type" => "json_object"];
            $options["messages"][] = ["role" => "user", "content" => "response in json format"];
            if ($this->jsonFormat) $options["messages"][] = ["role" => "user", "content" => "format example : " . json_encode($this->jsonFormat)];
        }
        return $options;
    }

    private function getCacheKey(): string
    {
        return 'ai_response_' . md5(json_encode($this->getOptions()));
    }
}
