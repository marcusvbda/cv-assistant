<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class MessageBoxItem extends Component
{
    public $message;

    public function mount($message)
    {
        $this->message = $message;
    }

    public function render()
    {
        return view('livewire.chat.message-box-item');
    }
}
