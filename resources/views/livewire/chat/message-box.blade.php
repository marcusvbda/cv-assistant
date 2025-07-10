@php 
use App\Livewire\Chat\MessageBox;
@endphp
<div>
    <div class="flex-1 overflow-y-auto mb-2">
        @foreach($messages as $key => $value)
            @livewire('chat.message-box-item', ['message' => $value], key($key))
        @endforeach
        @if($isProcessingAnswer)
           <div class="mb-1" wire:init="askForAnAnswer()">Processing answer...</div>
        @endif
    </div>
    <div>
        <form wire:submit.prevent="createMessageInThread($event.target.message.value)">
            <input type="text" name="message" class="w-full border p-2 disabled:opacity-10" wire:model.defer="newMessage"  @disabled($isProcessingAnswer) placeholder="Digite sua mensagem...">
        </form>
    </div>
</div>