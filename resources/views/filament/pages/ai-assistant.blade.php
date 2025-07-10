<x-filament-panels::page>
    <div class="md:grid md:grid-cols-4 gap-4 h-[600px]">
        {{-- <div class="bg-gray-100 p-4 overflow-y-auto">
            @livewire('chat.thread-list')
        </div> --}}
        <div class="col-span-4 bg-white p-4 flex flex-col">
            @livewire('chat.message-box') 
        </div>
    </div>
</x-filament-panels::page>