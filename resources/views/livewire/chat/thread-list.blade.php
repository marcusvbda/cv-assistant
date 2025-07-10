<!-- resources/views/livewire/chat/conversation-list.blade.php -->
<ul>
    @foreach($conversations as $conversation)
        <li class="p-2 border-b cursor-pointer hover:bg-gray-200">
            {{ $conversation->title }}
        </li>
    @endforeach
</ul>
