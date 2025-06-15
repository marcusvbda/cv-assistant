<x-filament::widget>
    <div wire:init="loadWidget">
        @if (! $readyToLoad)
            <x-filament::card>
                <div class="flex justify-center items-center py-4">
                    <x-filament::loading-indicator class="h-6" />
                </div>
            </x-filament::card>
        @else
            <x-filament::card>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold">Data registered for your CV</h2>
                        <span class="text-sm text-gray-500">{{ $percentage }}% Completed</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div
                            class="h-3 rounded-full transition-all duration-700 ease-in-out {{ 
                                $percentage >= 90 ? 'bg-green-500' : 
                                ($percentage >= 70 ? 'bg-yellow-400' : 'bg-red-400') 
                            }}"
                            style="width: {{ $percentage }}%">
                        </div>
                    </div>

                    <div class="text-gray-700 space-y-1 pt-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold">Quality of information</h2>
                        </div>
                        <p><strong>AI analysis:</strong> {{ $verdict }}</p>
                        <p><strong>AI score:</strong> <span class="font-semibold">{{ $score }}</span>/100</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div
                            class="h-3 rounded-full transition-all duration-700 ease-in-out {{ 
                                $scorePercentage >= 90 ? 'bg-green-500' : 
                                ($scorePercentage >= 70 ? 'bg-yellow-400' : 'bg-red-400') 
                            }}"
                            style="width: {{ $scorePercentage }}%">
                        </div>
                    </div>
                </div>
            </x-filament::card>
        @endif
    </div>
</x-filament::widget>
