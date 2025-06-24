<x-filament::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6"  wire:init="loadWidget">
       <div class="flex justify-between items-center w-full h-full">
            @if (! $readyToLoad)
                <div class="flex justify-center items-center w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                    <x-filament::loading-indicator class="h-8" />
                </div>
            @else
                <div class="flex justify-between items-center w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                    <div>
                                <h3 class="text-lg font-semibold">Quality of information</h3>
                                <p class="text-sm mt-2 text-gray-600">{{ $feedback }}</p>
                    </div>
                    <div class="flex-shrink-0 min-w-16 size-16 relative ml-6">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path
                                        class="text-gray-200"
                                        stroke-width="3"
                                        stroke="currentColor"
                                        fill="none"
                                        d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    />
                                    <path
                                        class="text-[#D97707]"
                                        stroke-width="3"
                                        stroke-dasharray="{{ $percentage }}, 100"
                                        stroke="currentColor"
                                        fill="none"
                                        d="M18 2.0845
                                        a 15.9155 15.9155 0 0 1 0 31.831
                                        a 15.9155 15.9155 0 0 1 0 -31.831"
                                    />
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-sm font-semibold">
                                    {{ $percentage }}%
                                </span>
                    </div>
                </div>
            @endif
       </div>
       @if (!$readyToLoad)
            <div class="flex justify-center items-center w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                <x-filament::loading-indicator class="h-8" />
            </div>
       @else
            <div class="w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold mb-8">Quality of information - {{$scorePercentage}}%</h3>
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
        @endif
    </div>
</x-filament::widget>
