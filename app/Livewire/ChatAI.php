<?php

namespace App\Livewire;

use App\Models\ChatAiThread;
use App\Services\AIService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;
use marcusvbda\GroqApiService\Services\GroqService;

class ChatAI extends Component
{
    public array $messages = [];
    public array $suggestions = [];
    public Bool $isProcessingAnswer = false;
    public ?Int $threadId = null;
    public String $newMessage = '';
    private GroqService $service;
    public int $page = 0;
    public bool $hasMore;
    private int $perPage = 4;
    public array $threads = [];
    private array $context = [];

    public function __construct()
    {
        $this->service = new GroqService([]);
        $this->context = $this->service->getThread();
        $this->createSuggestions();
        $threadId = request()->get("thread");
        if (!empty($threadId)) {
            $thread = ChatAiThread::findOrFail($threadId);
            $this->messages = $thread?->messages ?? [];
            $this->threadId = $thread?->id;
            $this->service->setThread(array_merge($this->context, $this->messages));
        }
    }

    private function createSuggestions(): void
    {
        $this->suggestions = [
            // __("Analyze my fit to this a description"),
            // __("Generate a Cover Letter"),
            // __("Generate a resume (CV)"),
        ];
    }

    public function askForAnAnswer(): void
    {
        $threadId = $this->threadId;
        $this->service->setThread(array_merge($this->context, $this->messages));
        $this->service->ask();
        $response = $this->service->getLastMessage();
        $content = data_get($response, 'content', '');
        $this->messages[] = ['role' => 'system', 'content' => $this->processAnswer($content)];
        ChatAiThread::where("id", $threadId)->update(["messages" => $this->messages]);
        $this->isProcessingAnswer = false;
    }

    private function processAnswer($response): mixed
    {
        return  $response;
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
            'content' =>  $this->newMessage
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
}
