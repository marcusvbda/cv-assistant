<div class="flex-shrink-0 relative ml-6 w-full h-full">
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
            class="transition-all duration-700 ease-in-out {{ 
                $percentage >= 90 ? 'text-green-500' : 
                ($percentage >= 70 ? 'text-yellow-400' : 'text-red-400') 
            }}"        
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