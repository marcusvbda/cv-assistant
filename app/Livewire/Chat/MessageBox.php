<?php

namespace App\Livewire\Chat;

use App\Models\User;
use App\Services\AIService;
use Livewire\Component;
use Auth;
use Str;

class MessageBox extends Component
{
    public $messages = [];
    public User $user;
    public $isProcessingAnswer = false;
    public $threadId;
    public string $newMessage = '';

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function sendMessageAndGetAnswer($messageUuid)
    {
        $messageIndex = array_search($messageUuid, array_column($this->messages, 'uuid'));
        $message = $this->messages[$messageIndex];
        $service = AIService::make()->user($message['text']);
        $threadId = data_get($message, 'thread_id');
        $messageId = data_get($message, 'uuid');
        $response = $service->generate("$threadId-$messageId-");
        $this->messages[$messageIndex]['answer_content'] = ["type" => "text", "content" => $response];
        $this->isProcessingAnswer = false;
    }

    public function createThreadIfNotExists()
    {
        if (empty($this->threadId)) {
            $this->threadId = (string) Str::uuid();
        }
    }

    public function createMessageInThread($text)
    {
        $this->createThreadIfNotExists();
        $newMessagePayload = [
            'user_id' => $this->user->id,
            'text' => $text,
            'uuid' => (string) Str::uuid(),
            'thread_id' => $this->threadId,
            'answer_content' => []
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
