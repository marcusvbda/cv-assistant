@php
    use Illuminate\Support\Str;
    use App\Livewire\ChatAI;
@endphp
<div>
    <div class="h-full grid grid-cols-1 gap-4 md:grid-cols-[300px_1fr]">
        <x-filament::card class="h-full">
            <h2 class="text-lg font-bold mb-2">@lang("Rencent threads")</h2>
            <x-filament::button wire:click="newThread" class="mb-4 w-full" size="sm" icon="heroicon-o-plus">
                @lang("New thread")
            </x-filament::button>
            <ul class="space-y-2 overflow-y-auto">
                @foreach ($threads as $thread)
                    @php
                        $title = data_get(collect(data_get($thread, 'messages', []))->where('role', 'user')->first() ?? [], 'content');
                        $currentId =  data_get($thread,"id");
                    @endphp
                    <li>
                        <x-filament::button
                            color="{{ $currentId === $threadId ? 'primary' : 'secondary' }}"
                            class="{{ $currentId === $threadId ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-700 !text-gray-800 dark:!text-gray-200' }} w-full"
                            wire:click="selectThread({{ $currentId }})"
                            size="sm"
                        >
                            {{ Str::limit($title, 30) }}
                        </x-filament::button>
                    </li>
                @endforeach
            </ul>
            @if ($hasMore)
                <x-filament::button wire:click="loadMore" class="mt-8 w-full" size="sm">
                    @lang("Load more")
                </x-filament::button>
            @endif
        </x-filament::card>

        <x-filament::card class="flex flex-col h-full">
            <div class="flex-1 overflow-y-auto space-y-4 px-2">
                @if(count($messages) > 0)
                    <h1 class="text-3xl font-bold mb-10">Chat @lang("AI")</h1>
                @endif
                @forelse ($messages as $message)
                    @php 
                        $isUser = data_get($message,'role') === 'user';
                        $content = data_get($message, 'content','');
                    @endphp
                    <div class="p-2 rounded-md {{ $isUser ? 'bg-primary-100 dark:bg-primary-500 text-right' : 'bg-gray-100 text-left dark:bg-gray-700' }} max-w-[80%] {{$isUser ? 'ml-auto' : 'mr-auto'}}">
                        <div class="text-sm text-gray-500 mb-1 dark:text-gray-100">
                            {{ __($isUser ? 'You' : 'Assistant') }}
                        </div>
                        <div class="text-sm">
                            {!! $content !!}
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full">
                        <h1 class="text-3xl font-bold my-10">Chat AI</h1>
                        <div class="flex items-center justify-center h-full gap-2 mb-10">
                            @foreach($suggestions as $key => $suggestion)
                                <button wire:click="createMessageInThread('{{ $suggestion }}')" class="border p-2 rounded-lg">
                                    {{ $suggestion }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforelse
                @if($isProcessingAnswer)
                    <div  class="flex items-center justify-center p-4"  wire:init="askForAnAnswer()">
                        <x-filament::loading-indicator class="size-8 opacity-20"/>
                    </div>
                @endif
            </div>
            <form wire:submit.prevent="createMessageInThread" class="flex items-center space-x-2 border border-gray-200 dark:border-gray-600 p-2 rounded-lg mt-10">
                <x-filament::input
                    wire:model.defer="newMessage"
                    placeholder="{{__('Type your message here')}}..."
                    :bordered="true"
                    class="flex-1" wire:loading.attr="disabled"
                />
                <x-filament::button type="submit"   wire:loading.attr="disabled">
                    @lang("Send")
                </x-filament::button>
            </form>
        </x-filament::card> 
    </div>
</div>
@script
<script>
    $wire.on('add-url-param', data => {
        const url = new URL(window.location);
        (data || []).forEach(row => {
            url.searchParams.set(row.param, row.value);
        });
        window.history.pushState({}, '', url);
    });
</script>
@endscript