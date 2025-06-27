<div class="flex items-center gap-2 text-xs">
    <div class="w-full bg-gray-200 rounded-full h-3">
        <div
            class="h-3 rounded-full transition-all duration-700 ease-in-out {{ 
                $percentage >= 90 ? 'bg-green-500' : 
                ($percentage >= 70 ? 'bg-yellow-400' : 'bg-red-400') 
            }}"
            style="width: {{ $percentage }}%">
        </div>
    </div>
    {{ $percentage }}%  
</div>