<?php

namespace App\Livewire\Chat;

use App\Models\ChatAiThread;
use App\Services\AIService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MessageBox extends Component
{
    public array $messages = [];
    public array $suggestions = [];
    public Bool $isProcessingAnswer = false;
    public Int $threadId;
    public String $newMessage = '';
    private AIService $service;
    const ANSWER_TYPE_TEXT = '_TEXT_';
    const ANSWER_TYPE_FUNCTION = '_FUNCTION_';
    const ANSWER_TYPE_HTML = '_HTML_';

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
        $provider = $this->service->getProvider();
        $messagesPayload = $this->messages;
        if ($provider === "openai") {
            $messagesPayload = [end($messagesPayload)];
        }

        $this->service->setMessages($messagesPayload);
    }

    private function processAnswer($response): mixed
    {
        return data_get($response, 'content');
    }

    public function createThreadIfNotExists(): void
    {
        if (empty($this->threadId)) {
            $thread = ChatAiThread::create([
                'messages' => [],
            ]);

            $this->threadId = $thread->id;
        }
    }

    public function createMessageInThread($text): void
    {
        $this->createThreadIfNotExists();
        $newMessagePayload = [
            'role' => 'user',
            'content' => $text,
        ];
        $this->messages[] = $newMessagePayload;
        $this->isProcessingAnswer = true;
        $this->newMessage = '';
    }

    public function render(): View
    {
        return view('livewire.chat.message-box');
    }
}
