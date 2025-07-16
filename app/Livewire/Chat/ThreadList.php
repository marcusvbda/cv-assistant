<?php

namespace App\Livewire\Chat;

use App\Models\ChatAiThread;
use Livewire\Component;

class ThreadList extends Component
{
    public int $page = 0;
    public bool $hasMore;
    private int $perPage = 2;
    public array $data = [];

    public function loadMore()
    {
        $this->page++;
        $itemsPaginated = ChatAiThread::orderBy("created_at", "desc")->paginate($this->perPage, ["*"], "page", $this->page);
        $this->hasMore = $itemsPaginated->hasMorePages();
        $newItems = $itemsPaginated->toArray()['data'];
        $this->data = array_merge($this->data, $newItems);
    }

    public function mount()
    {
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.chat.thread-list', [
            'threads' => $this->data
        ]);
    }
}
