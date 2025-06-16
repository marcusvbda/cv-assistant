<x-filament-panels::page>
    @if(!Auth::user()->hasAiIntegration())
        <x-filament::card>
            <div class="flex items-center space-x-3">
                <x-filament::icon icon="heroicon-o-exclamation-circle" color="danger" class="h-6"/>
                <div>
                    <p class="text-sm font-medium text-danger-600">
                        AI Provider Key Missing
                    </p>
                    <p class="text-sm text-gray-600">
                        Please configure your AI provider key to unlock AI-powered features in the system.
                    </p>
                </div>
            </div>
        </x-filament::card>
    @endif
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <x-filament::button type="submit" class="mt-6" >
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>