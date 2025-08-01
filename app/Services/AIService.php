<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;
use Auth;

class AIService
{
    private $messages;
    private $provider;
    private $key;
    private $user;
    private $isJson = false;
    private $jsonFormat;
    private $model;
    private $temperature = 0.7;

    public function __construct($messages)
    {
        $this->user = Auth::user();
        $this->bootstrap();
        $this->messages = $messages;
    }

    private function bootstrap()
    {
        $ai_integration = $this->user->ai_integration ?? [];
        $this->key = data_get($ai_integration, "key");
        $this->setProvider(data_get($ai_integration, "provider"));
    }

    public function setUser($user): self
    {
        $this->user = $user;
        $this->bootstrap();
        return $this;
    }

    public function setProvider($provider): self
    {
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

    public function setMessages($value): self
    {
        $this->messages = $value;
        return $this;
    }

    public function json($format = null): self
    {
        $this->isJson = true;
        if ($format) $this->jsonFormat = $format;
        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    private function getModel(): string
    {
        return data_get([
            "groq" => "meta-llama/llama-4-maverick-17b-128e-instruct",
            "openai" => "gpt-4o-mini"
        ], $this->provider);
    }

    private function getProviderUrl()
    {
        $url = data_get([
            "groq" => "https://api.groq.com/openai/v1",
            "openai" => "https://api.openai.com/v1",
        ], $this->provider);
        return $url;
    }


    public function generate($responseIndex = 'choices.0.message.content', $cacheKeyPrefix = ''): mixed
    {
        $cacheKey = implode("-", array_filter([$cacheKeyPrefix, $this->getCacheKey(), $responseIndex]));

        try {
            $url = $this->getProviderUrl();
            config(['openai.base_uri' => $url]);
            config(['openai.api_key' => $this->key]);
            $this->model = $this->getModel();

            return Cache::rememberForever($cacheKey, function () use ($responseIndex) {
                $payload = $this->getOptions();
                $response = OpenAI::chat()->create($payload);
                $result = data_get($response, $responseIndex, '');
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
            'temperature' => $this->temperature,
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
        return 'ai_response_' . md5(json_encode($this->getOptions()) . $this->key . $this->provider . $this->getModel());
    }
}
