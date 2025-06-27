<x-filament::widget>
    @if (! $readyToLoad)
        <div class="flex justify-center items-center w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6" wire:init="loadWidget">
            <x-filament::loading-indicator class="h-6" />
        </div>
    @else
         <div class="flex justify-between items-center w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
            <div>
                <h3 class="text-lg font-semibold">Linkedin AI Analysis</h3>
                <p class="text-sm mt-2 text-gray-600">{{ $feedback }}</p>
            </div>
            <div class="max-w-[100px]">
                @include("components.donut-chart",["percentage" => $score])
            </div>
         </div>
     @endif
</x-filament::widget>
