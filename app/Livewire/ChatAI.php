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
            __("Analyze my fit to this a description"),
            __("Generate a Cover Letter"),
            __("Generate a resume (CV)"),
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
        $content = data_get($response, 'content', '');
        try {
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
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
                'content' => "Error processing the response: " . $e->getMessage() . "\n\n" . $content
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
        return redirect()->to("/admin/ai-assistant");
    }

    public function selectThread(int $threadId): Redirector|RedirectResponse
    {
        return redirect()->to("/admin/ai-assistant?thread={$threadId}");
    }

    public function render(): View
    {
        return view('livewire.chat-ai');
    }

    private function getFunctions(): string
    {
        return json_encode([
            [
                "name" => "getFitToAJobDescription",
                "description" => "Returns the user's fit with a job position.",
                "questions_before" => ["Ask to user copy and past the job description in the chat box."],
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "description" => [
                            "type" => "string",
                            "description" => "Job description provided by the user.",
                        ],
                        "language" => [
                            "type" => "string",
                            "description" => "Based in this thread language will be used to generate the response.",
                        ],
                    ],
                    "required" => ["description", "language"],
                ]
            ],
            [
                "name" => "generateCoverLetter",
                "description" => "Generates a Cover Letter based on a job description or not.",
                "questions_before" => [
                    "Say to user if he wants to generate a cover letter to a specific job description copy and past the job description in the chat box.",
                    "Ask if the user wants to specify something else."
                ],
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "description" => [
                            "type" => "string",
                            "description" => "Job description provided by the user.",
                        ],
                        "extra_specifications" => [
                            "type" => "string",
                            "description" => "Extra specifications provided by the user."
                        ],
                        "language" => [
                            "type" => "string",
                            "description" => "Based in this thread language will be used to generate the response.",
                        ]
                    ],
                    "required" => ["description", "language"],
                ]
            ],
            [
                "name" => "generateResume",
                "description" => "Generates a resume (CV) based on a job description or not.",
                "questions_before" => [
                    "Say to user if he wants to generate a resume to a specific job description copy and past the job description in the chat box.",
                    "Ask if the user wants to specify something else."
                ],
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "description" => [
                            "type" => "string",
                            "description" => "Job description provided by the user.",
                        ],
                        "extra_specifications" => [
                            "type" => "string",
                            "description" => "Extra specifications provided by the user."
                        ],
                        "language" => [
                            "type" => "string",
                            "description" => "Based in this thread language will be used to generate the response.",
                        ]
                    ],
                    "required" => ["description", "language"],
                ]
            ]
        ]);
    }

    private function generateResume(array $params): string
    {
        $description = data_get($params, 'description', 'no description provided');
        $extra_specifications = data_get($params, 'extra_specifications', 'no extra specifications provided');
        $language = data_get($params, 'language', 'english');

        return json_encode([
            "type" => static::ANSWER_TYPE_TEXT,
            "content" => "aqui sua $description $extra_specifications $language"
        ]);
    }

    private function generateCoverLetter(array $params): string
    {
        $messagesPayload = json_encode([
            "requesting_parameters" => $params,
            "user_info" => auth()->user()->getPayloadContext()
        ]);

        $result = AIService::make()
            ->system("Based on this information : $messagesPayload, generate a good cover letter otimized for this job and ATS.")
            ->system("Respond only with valid HTML code without any other text.")
            ->system("NEVER respond a html tag without scaping it and close it properly.")
            ->system("NEVER respond scripts or styles, If you are responding a styled content use inline styles.")
            ->system("NEVER include <html>, <head>, <pre>, <body>,```html tags or any full-page HTML structure in the response.")
            ->generate();

        return json_encode([
            "type" => static::ANSWER_TYPE_HTML,
            "content" => html_entity_decode($result)
        ]);
    }

    private function getFitToAJobDescription(array $params): string
    {
        $description = data_get($params, 'description', 'no description provided');
        $language = data_get($params, 'language', 'english');

        return json_encode([
            "type" => static::ANSWER_TYPE_TEXT,
            "content" => "aqui sua $description $language"
        ]);
    }

    private function getInitialContext(): array
    {
        $functions = $this->getFunctions();
        $user = auth()->user();
        $contextPayload = json_encode($user->getPayloadContext());

        return [
            [
                "role" => "system",
                "content" => <<<EOT
                    You are a strict AI assistant that must ALWAYS respond with a SINGLE, VALID, and WELL-FORMED JSON in the following format:
                    {"type": "_TYPE_", "content": "..."}
                    Valid values for "type" are: "_TEXT_", "_HTML_", and "_FUNCTION_".
                EOT
            ],
            [
                "role" => "system",
                "content" => <<<EOT
                    ABSOLUTE RULES (NEVER BREAK THESE):
                    - NEVER respond with broken, malformed, or incomplete JSON.
                    - NEVER include multiple JSON blocks in a single response.
                    - NEVER add extra text, markdown, comments, explanations, or newlines before or after the JSON.
                    - ALWAYS ensure proper opening and closing of brackets.
                    - NEVER invent or assume unknown function names or parameters.
                    - NEVER use "_FUNCTION_" unless the function is explicitly listed and fully applicable.
                    - NEVER respond with HTML tags without escaping them and closing them properly.
                    - NEVER include scripts or styles. Use inline styles only if needed.
                    - NEVER include <html>, <head>, <pre>, <body>, ```html tags, or full-page HTML structures.
                EOT
            ],
            [
                "role" => "system",
                "content" => <<<EOT
                    FUNCTION USAGE:
                    - ONLY use "_FUNCTION_" if:
                    - The function is explicitly defined in the list below.
                    - The function fully satisfies the user's request.
                    - All required parameters are available and correctly filled.
                    - All "questions_before" (if any) have been asked and answered.
                    - When a function is valid and applicable, respond strictly using:
                    {
                        "type": "_FUNCTION_",
                        "content": {
                        "name": "function_name",
                        "parameters": { ... }
                        }
                    }
                    - DO NOT use "_TEXT_" or "_HTML_" if a valid function can be used.
                    - If a function has unanswered "questions_before", ask them before using the function.
                EOT
            ],
            [
                "role" => "system",
                "content" => <<<EOT
                    OTHER RESPONSE TYPES:
                    - Use "_TEXT_" for plain replies when no function applies.
                    - Use "_HTML_" only when formatting is essential, and only with inline styles.
                    - If you are unsure how to respond, use:
                    {"type": "_TEXT_", "content": "I don't know."}
                EOT
            ],
            [
                "role" => "system",
                "content" => <<<EOT
                    CONTEXT:
                    Consider all provided functions and user context regardless of language used in the last message.
                    User context:
                    $contextPayload
                EOT
            ],
            [
                "role" => "system",
                "content" => "AVAILABLE FUNCTIONS: $functions"
            ],
            [
                "role" => "system",
                "content" => <<<EOT
                    SPECIAL NOTE:
                    When the user asks to rephrase, translate, or reformat a previous answer, use context memory instead of generating a new function response — use "_TEXT_" or "_HTML_" as appropriate.
                EOT
            ],
            [
                "role" => "system",
                "content" => <<<EOT
                    REMEMBER: Always respond with a single valid JSON using one of the allowed types. Never wrap or explain the JSON. Do not break these rules.
                EOT
            ]
        ];
    }
}
