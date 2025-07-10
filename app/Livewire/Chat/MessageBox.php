<?php

namespace App\Livewire\Chat;

use App\Models\ChatAiThread;
use App\Services\AIService;
use Livewire\Component;
use Str;

class MessageBox extends Component
{
    public $messages = [];
    public $isProcessingAnswer = false;
    public $threadId;
    public string $newMessage = '';
    const ANSWER_TYPE_TEXT = '_TEXT_';

    private function makeService()
    {
        $messages = $this->messages;
        $service = new AIService($messages);
        return $service;
    }

    public function askForAnAnswer()
    {
        $threadId = $this->threadId;
        $service = $this->makeService();
        $response = $service->generate("choices.0.message");
        $this->messages[] = ['role' => 'system', 'content' => $this->processAnswer($response)];
        ChatAiThread::where("id", $threadId)->update(["messages" => $this->messages]);
        $this->isProcessingAnswer = false;
    }

    private function processAnswer($response)
    {
        return data_get($response, 'content');
    }

    public function createThreadIfNotExists()
    {
        if (empty($this->threadId)) {
            $thread = ChatAiThread::create([
                'messages' => []
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
