<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ThreadList extends Component
{
    public function render()
    {
        return view('livewire.chat.thread-list', [
            'conversations' => []
            // \App\Models\Conversation::all()
        ]);
    }
}
