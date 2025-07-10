@php 
use App\Livewire\Chat\MessageBox;
@endphp
<div>
    <div class="flex-1 overflow-y-auto mb-2">
        @foreach($messages as $message)
            <div class="mb-1">
                <strong>You :</strong> {{ data_get($message,"text") }}
            </div>
            @if(data_get($message,"answer_content") === MessageBox::CONTENT_STATUS_PROCESSING)
                <div class="mb-1" wire:init="sendMessageAndGetAnswer('{{data_get($message,"uuid")}}')">Processing answer...</div>
            @endif
            @if(data_get($message,"answer_content.type") === MessageBox::ANSWER_TYPE_TEXT)
                <div class="mb-1">
                    <strong>AI:</strong> {{ data_get($message,"answer_content.content") }}
                </div>
            @endif
        @endforeach
    </div>
    <div>
        <form wire:submit.prevent="createMessageInThread($event.target.message.value)">
            <input type="text" name="message" class="w-full border p-2 disabled:opacity-10" wire:model.defer="newMessage"  @disabled($isProcessingAnswer) placeholder="Digite sua mensagem...">
        </form>
    </div>
</div>