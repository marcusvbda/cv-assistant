<?php

namespace App\Livewire\Chat;

use App\Models\ChatAiThread;
use App\Services\AIService;
use Livewire\Component;

class MessageBox extends Component
{
    public $messages = [];
    public $isProcessingAnswer = false;
    private AIService $service;
    public $threadId;
    public $providerThreadId;
    public string $newMessage = '';
    const ANSWER_TYPE_TEXT = '_TEXT_';
    const ANSWER_TYPE_FUNCTION = '_FUNCTION_';
    const ANSWER_TYPE_HTML = '_HTML_';

    public function __construct()
    {
        $this->service = new AIService([]);
        $providerThreadId = "thread_hORCr94FxZWd0vE0omvvbuV6";
        if (!empty($providerThreadId)) {
            $thread = ChatAiThread::where('provider_thread_id', $providerThreadId)->first();
            $this->messages = $thread?->messages ?? [];
            $this->threadId = $thread?->id;
            $this->providerThreadId = $providerThreadId;
        }
    }

    public function askForAnAnswer()
    {
        $threadId = $this->threadId;
        $this->setServicePayload();
        $response = $this->service->generate("choices.0.message");
        $this->messages[] = ['role' => 'system', 'content' => $this->processAnswer($response)];
        ChatAiThread::where("id", $threadId)->update(["messages" => $this->messages]);
        $this->isProcessingAnswer = false;
    }

    private function setServicePayload()
    {
        $provider = $this->service->getProvider();
        $messagesPayload = $this->messages;
        if ($provider === "openai") {
            $messagesPayload = [end($messagesPayload)];
        }

        $this->service->setMessages($messagesPayload);
    }

    private function processAnswer($response)
    {
        return data_get($response, 'content');
    }

    public function createThreadIfNotExists()
    {
        if (empty($this->threadId) || empty($this->providerThreadId)) {
            $provider = $this->service->getProvider();
            $providerThreadId = null;
            if ($provider === 'openai') {
                $providerThreadId =  $this->providerThreadId ?? $this->service->createThread();
            }
            $thread = ChatAiThread::create([
                'messages' => [],
                'provider_thread_id' => $providerThreadId
            ]);

            $this->threadId = $thread->id;
        }
    }

    public function createMessageInThread($text)
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

    public function render()
    {
        return view('livewire.chat.message-box');
    }
}
