<x-filament::widget>
    <div wire:init="loadWidget">
        <x-filament::card>
            @if (! $readyToLoad)
                <div class="flex justify-center items-center py-4">
                    <x-filament::loading-indicator class="h-6" />
                </div>
            @else
                @if($hasIntegration)
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold">Linkedin AI Analysis</h2>
                        <div class="text-gray-700 space-y-1">
                            <p><strong>AI analysis:</strong> {{ $verdict }}</p>
                            <p><strong>AI score:</strong> <span class="font-semibold">{{ $score }}</span>/100</p>
                        </div>
                    </div>
                @else
                   @include('filament.alerts.ai-not-configured')
                @endif
            @endif
        </x-filament::card>
    </div>
</x-filament::widget>
