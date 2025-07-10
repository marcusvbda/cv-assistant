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
    const CONTENT_STATUS_PROCESSING = '_PROCESSING_';


    public function sendMessageAndGetAnswer($messageUuid)
    {
        $threadId = $this->threadId;
        $messageIndex = array_search($messageUuid, array_column($this->messages, 'uuid'));
        $message = $this->messages[$messageIndex];
        $service = AIService::make()->user(data_get($message, 'text'));
        $messageUuId = data_get($message, 'uuid');
        $response = $service->generate("choices.0.message", "$threadId-$messageUuId-");
        $this->messages[$messageIndex]['answer'] = $this->processAnswer($response);
        ChatAiThread::where("id", $threadId)->update(["messages" => $this->messages]);
        $this->isProcessingAnswer = false;
    }

    private function processAnswer($response)
    {
        return ["type" => static::ANSWER_TYPE_TEXT, "content" => data_get($response, 'content')];
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
            'text' => $text,
            'uuid' => (string) Str::uuid(),
            'answer' => static::CONTENT_STATUS_PROCESSING
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
