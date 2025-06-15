<x-filament-panels::page>
        <div class="bg-white shadow rounded-xl">
           <iframe 
                src="{{ route('resume.stream', ['user' => $user->id]) }}" 
                width="100%" 
                height="1200" 
                style="border:none;"
            ></iframe>
        </div>
</x-filament-panels::page>