<div class="flex flex-col gap-2">
    <div class="flex-1 mb-2 overflow-y-auto min-h-[600px] h-[600px]">
        @if(count($messages)) 
            @foreach($messages as $key => $value)
                @livewire('chat.message-box-item', ['message' => $value], key($key))
            @endforeach
        @else 
            <div class="flex items-center justify-center h-full gap-2">
                @foreach($suggestions as $key => $suggestion)
                    <button wire:click="createMessageInThread('{{ $suggestion }}')" class="border p-2 rounded-lg">
                        {{ $suggestion }}
                    </button>
                @endforeach
           </div>
        @endif
        @if($isProcessingAnswer)
            <div  class="flex items-center justify-center p-4"  wire:init="askForAnAnswer()">
                <x-filament::loading-indicator class="size-8 opacity-20"/>
            </div>
        @endif
    </div>
    <div>
        <form wire:submit.prevent="createMessageInThread($event.target.message.value)">
            <input type="text" name="message" class="w-full border p-2 disabled:opacity-70" wire:model.defer="newMessage"  @disabled($isProcessingAnswer) placeholder="Type your message here...">
        </form>
    </div>
</div>