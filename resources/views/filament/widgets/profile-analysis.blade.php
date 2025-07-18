<x-filament::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6"  wire:init="loadWidget">
       <div class="flex justify-between items-center w-full h-full">
            @if (! $readyToLoad)
                <div class="flex justify-center items-center w-full h-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                    <x-filament::loading-indicator class="h-8" />
                </div>
            @else
                <div class="flex justify-between items-center w-full h-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                    <div>
                        <h3 class="text-lg font-semibold">Profile completition</h3>
                        <p class="text-sm mt-2 text-gray-600 dark:text-gray-400">{{ $feedback }}</p>
                    </div>
                    <div class="max-w-[100px]">
                        @include("components.donut-chart",["percentage" => $scorePercentage])
                    </div>
                </div>
            @endif
       </div>
       @if (!$readyToLoad)
            <div class="flex justify-center items-center w-full h-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                <x-filament::loading-indicator class="h-8" />
            </div>
       @else
            <div class="w-full h-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-8">AI analysis</h3>
                    <p class="text-sm mt-2 text-gray-600">Score generated with AI based on your profile.</p>
                </div>
                @include("components.progress-bar",["percentage" => $scorePercentage])
            </div>
        @endif
    </div>
</x-filament::widget>
