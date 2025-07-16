<!-- resources/views/livewire/chat/conversation-list.blade.php -->
<div class="overflow-y-auto min-h-[500px] h-[500px]">
    <a href="/ai-assistant">New thread</a>
    <ul>
        <h4 class="text-lg font-semibold">Threads</h4>
        @foreach($threads as $thread)
            <li class="p-2 border-b cursor-pointer hover:bg-gray-200 overflow-hidden whitespace-nowrap text-ellipsis">
                <a href="?thread={{ data_get($thread,'id') }}">
                    {{ data_get($thread,'messages.0.content') }}
                </a>
            </li>
        @endforeach
    </ul>
    @if($hasMore)
        <a href="javascript:void(0)" wire:click="loadMore" >
            Load more ({{$page}})
        </a>
    @endif
</div>