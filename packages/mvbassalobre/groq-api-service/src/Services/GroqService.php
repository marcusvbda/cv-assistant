<?php

namespace Mvbassalobre\GroqApiService\Services;

use BadMethodCallException;
use Mvbassalobre\GroqApiService\Settings\GroqApiServiceSettings;
use League\HTMLToMarkdown\HtmlConverter;
use OpenAI\Laravel\Facades\OpenAI;

class GroqService
{
    private $settings = [];
    protected array $dynamicMethods = [];

    public function __construct(array $payload = [])
    {
        $initialThread = data_get($payload, 'thread', []);
        $this->settings = array_merge(config('groq-api-service'), app()->make(GroqApiServiceSettings::class)->settings, [
            'thread' => $initialThread
        ], $payload);

        $this->makeGetters();
        $this->makeSetters();

        if (empty($initialThread)) {
            $this->setThread($this->makeContextualizedThread());
        }
    }

    public function makeGetters(): void
    {
        foreach ($this->settings as $key => $value) {
            $functionGet = fn() =>  data_get($this->settings, $key);
            $keyName = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            $this->dynamicMethods["get$keyName"] = $functionGet->bindTo($this, $this);
        }
    }

    public function makeSetters(): void
    {
        foreach ($this->settings as $key => $value) {
            $functionSet = fn($newValue) =>  $this->settings[$key] = $newValue;
            $keyName = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            $this->dynamicMethods["set$keyName"] = $functionSet->bindTo($this, $this);
        }
    }

    public function __call($name, $arguments): mixed
    {
        if (isset($this->dynamicMethods[$name])) {
            return call_user_func_array($this->dynamicMethods[$name], $arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist.");
    }

    public function message($role, $text): self
    {
        $thread = $this->getThread();
        $thread[] = [
            "role" => $role,
            "content" => $text
        ];
        $this->setThread($thread);
        return $this;
    }

    public function user($text): self
    {
        return $this->message('user', $text);
    }

    public function system($text): self
    {
        return $this->message('system', $text);
    }

    private function htmlToMarkdown(string $html): string
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => 'script style'
        ]);

        return $converter->convert($html);
    }

    public function makeContextualizedThread(): array
    {
        $contextArray = [
            "## RULES",
            "- Your name is {$this->getBotName()}, and you are a helpful assistant that must answer just following the strictly *instructions and context* below.",
            "- *Never* break these rules!",
            "- *Never* answer anything that is not related to the context.",
            "## MAIN CONTEXT \n{$this->htmlToMarkdown($this->getMainContext())}",
            "## INITIAL INSTRUCTIONS \nInitialy, consider the following instructions:\n{$this->htmlToMarkdown($this->getInitialInstructions())}",
            "## ABSOLUTE RULES \nYou *must* the these rules strictly:\n{$this->htmlToMarkdown($this->getMainContext())}",
            "## EXPECTED RESPONSE FORMAT \n{$this->htmlToMarkdown($this->getExpectedResponseType())}",
        ];
        return [[
            "role" => "system",
            "content" => implode("\n", $contextArray)
        ]];
    }

    public function getLastMessage(): mixed
    {
        $thread = $this->getThread();
        return empty($thread) ? null : $thread[count($thread) - 1];
    }

    public function ask(): self
    {
        try {
            config(['openai.base_uri' => $this->getBaseUrl()]);
            config(['openai.api_key' => $this->getKey()]);
            $response = OpenAI::chat()->create([
                'model' => $this->getModel(),
                'messages' => $this->getThread(),
                'temperature' => $this->getTemperature(),
            ]);
            $this->system(data_get($response, 'choices.0.message.content', ''));
            return $this;
        } catch (\Throwable $e) {
            $this->system($e->getMessage());
            return $this;
        }
    }
}
