<x-filament-panels::page>
    <div class="grid grid-cols-[1fr_4fr]">
        <div class="bg-gray-100 p-4 overflow-y-auto">
            @livewire('chat.thread-list')
        </div>
        <div class="bg-white p-4 flex flex-col">
            @livewire('chat.message-box') 
        </div>
    </div>
</x-filament-panels::page>

