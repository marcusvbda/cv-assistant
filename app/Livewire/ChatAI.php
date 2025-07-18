<?php

namespace App\Livewire;

use App\Models\ChatAiThread;
use App\Services\AIService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class ChatAI extends Component
{
    public array $messages = [];
    public array $suggestions = [];
    public Bool $isProcessingAnswer = false;
    public ?Int $threadId = null;
    public String $newMessage = '';
    private AIService $service;
    public int $page = 0;
    public bool $hasMore;
    private int $perPage = 4;
    public array $threads = [];
    public const ANSWER_TYPE_TEXT = '_TEXT_';
    public const ANSWER_TYPE_FUNCTION = '_FUNCTION_';
    public const ANSWER_TYPE_HTML = '_HTML_';

    public function __construct()
    {
        $this->service = new AIService([]);
        $this->createSuggestions();
        $threadId = request()->get("thread");
        if (!empty($threadId)) {
            $thread = ChatAiThread::findOrFail($threadId);
            $this->messages = $thread?->messages ?? [];
            $this->threadId = $thread?->id;
        }
    }

    private function createSuggestions(): void
    {
        $this->suggestions = [
            "Say just \"ok\"",
            "Say hello",
            "Say hello world",
            "Test",
        ];
    }

    public function askForAnAnswer(): void
    {
        $threadId = $this->threadId;
        $this->setServicePayload();
        $response = $this->service->generate("choices.0.message", "");
        $this->messages[] = ['role' => 'system', 'content' => $this->processAnswer($response)];
        ChatAiThread::where("id", $threadId)->update(["messages" => $this->messages]);
        $this->isProcessingAnswer = false;
    }

    private function setServicePayload(): void
    {
        $messagesPayload = array_merge($this->getInitialContext(), $this->messages);
        $this->service->setMessages($messagesPayload);
    }

    private function processAnswer($response): mixed
    {
        try {
            $json = json_decode(data_get($response, 'content', ''), true, 512, JSON_THROW_ON_ERROR);
            if (!isset($json['type']) || !isset($json['content'])) {
                throw new \Exception("Invalid response format: missing 'type' or 'content'");
            }

            $type = $json['type'];
            if (!in_array($type, [
                static::ANSWER_TYPE_TEXT,
                static::ANSWER_TYPE_FUNCTION,
                static::ANSWER_TYPE_HTML
            ])) {
                throw new \Exception("Invalid type: $type");
            }

            if ($type === static::ANSWER_TYPE_FUNCTION) {
                $functionName = data_get($json, 'content.name');
                $parameters = data_get($json, 'content.parameters', []);
                return $this->{$functionName}($parameters);
            }

            return json_encode($json);
        } catch (\Throwable $e) {
            return json_encode([
                'type' => static::ANSWER_TYPE_TEXT,
                'content' => "Error processing the response: " . $e->getMessage()
            ]);
        }
    }

    public function createThreadIfNotExists($newMessagePayload): void
    {
        if (empty($this->threadId)) {
            $thread = ChatAiThread::create([
                'messages' => [
                    $newMessagePayload
                ],
            ]);

            $this->threadId = $thread->id;
            $this->threads = array_merge([$thread->toArray()], $this->threads);

            $this->dispatch('add-url-param', [
                'param' => 'thread',
                'value' => $this->threadId
            ]);
        }
        $this->messages[] = $newMessagePayload;
    }

    public function createMessageInThread($val = null): void
    {
        $this->createThreadIfNotExists([
            'role' => 'user',
            'content' => json_encode([
                'type' => static::ANSWER_TYPE_TEXT,
                'content' => $val ?? $this->newMessage
            ]),
        ]);
        $this->isProcessingAnswer = true;
        $this->newMessage = '';
    }

    public function loadMore()
    {
        $this->page++;

        $itemsPaginated = ChatAiThread::where('id', '!=', $this->threadId)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        $this->hasMore = $itemsPaginated->hasMorePages();

        $currentThread = $this->threadId
            ? ChatAiThread::find($this->threadId)
            : null;

        $allThreads = collect()
            ->when($currentThread, fn($c) => $c->push($currentThread))
            ->merge($this->threads)
            ->merge($itemsPaginated->items())
            ->unique('id')
            ->values()
            ->toArray();

        $this->threads = $allThreads;
    }

    public function mount()
    {
        $this->loadMore();
    }

    public function newThread(): Redirector|RedirectResponse
    {
        return redirect()->to("/ai-assistant");
    }

    public function selectThread(int $threadId): Redirector|RedirectResponse
    {
        return redirect()->to("/ai-assistant?thread={$threadId}");
    }

    public function render(): View
    {
        return view('livewire.chat-ai');
    }

    private function getFunctions(): string
    {
        return json_encode([
            [
                "name" => "getLoremIpsumTestFunction",
                "description" => "Returns a lorem ipsum test text with current date/time. Use this when the user asks for: test, run test, test message, placeholder content, dummy text, lorem ipsum.",
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "currentDate" => [
                            "type" => "string",
                            "description" => "Current date in format YYYY-MM-DD (e.g. 2025-07-18)"
                        ],
                        "currentTime" => [
                            "type" => "string",
                            "description" => "Current time in format HH:MM:SS (e.g. 13:45:00)"
                        ],
                    ],
                    "required" => ["currentDate", "currentTime"]
                ]
            ]

        ]);
    }

    private function getLoremIpsumTestFunction(array $params): string
    {
        $currentDate = $params['currentDate'] ?? date("Y-m-d");
        $currentTime = $params['currentTime'] ?? date("H:i:s");

        return json_encode([
            "type" => static::ANSWER_TYPE_TEXT,
            "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Current date: {$currentDate}, Current time: {$currentTime}."
        ]);
    }

    private function getInitialContext(): array
    {
        $functions = $this->getFunctions();

        return [
            [
                'role' => 'system',
                'content' => <<<EOT
                You are a strict AI assistant that ONLY responds with valid JSON. Your responses MUST follow this exact format:

                {"type": "_TYPE_", "content": "..."}

                Valid values for "type" JUST are:
                - _TEXT_
                - _HTML_
                - _FUNCTION_

                Rules:
                - NEVER add extra text, markdown, or explanations.
                - Consider all the context and functions provided independently of the language of the user.
                - NEVER include multiple JSON blocks.
                - NEVER add newline before or after the JSON.
                - If you use "_FUNCTION_", the "name" field must match exactly a defined function name.
                - Do NOT invent function names or parameters.
                - If no defined function applies, respond using "_TEXT_" or "_HTML_".
                - If you are unsure, respond with: {"type": "_TEXT_", "content": "I don't know."}
                - In case of "_HTML_", use only inline styles.

                ### Functions available for the AI assistant:
                You may ONLY use "_FUNCTION_" type if one of these functions fully solves the user's request:
                1. If the user's request **can be fully answered using one of the defined functions**, you MUST respond using:
                    {
                        "type": "_FUNCTION_",
                        "content" : {
                            "name": "function_name",
                            "parameters": { ... }
                        }
                    }

                2. NEVER use "_TEXT_" or "_HTML_" if a valid function is available and applicable.

                3. ONLY use "_FUNCTION_" if:
                - The function is explicitly listed;
                - You are confident it applies directly;
                - You fill all required parameters properly.
                - Consider the functions bellow:
                    $functions

                4. If no function is applicable, respond using "_TEXT_" or "_HTML_" depending on context.

                5. If unsure, respond with:
                {
                    "type": "_TEXT_",
                    "content": "I don't know."
                }

            EOT
            ],
        ];
    }
}
