<x-filament-panels::page>
    <div class="space-y-4">
        <div class="bg-white shadow rounded-xl p-4">
            <h2 class="text-lg font-bold mb-10">Preview</h2>
            <p><strong>Nome:</strong> {{ $this->name }}</p>
            <p><strong>E-mail:</strong> {{ $this->email }}</p>
        </div>
    </div>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-6" >
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>