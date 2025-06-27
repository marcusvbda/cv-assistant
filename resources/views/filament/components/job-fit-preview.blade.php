@php
    use App\Enums\JobDescriptionAnalysisStatusEnum;
    $details = $item->jobApplyDetail()->first();
@endphp
@if(!$user->hasAiIntegration())
    @include("filament.components.ai-not-configured")
@else
    <div class="flex justify-between items-center w-full h-full bg-white rounded-xl border border-gray-200/70 dark:border-gray-700 p-6">
        <div>
            <h3 class="text-lg font-semibold">Fit analysis</h3>
            <p class="text-sm mt-2 text-gray-600">{{ $details?->comment ?? '' }}</p>
            @if($details?->id)
                <p class="text-sm text-gray-600 flex flex-col md:flex-row gap-4 mt-10">
                    <a class="text-primary-600" href="{{route('download.pdf', ['jobApplyDetail' => $details->id, 'type' => 'resume'])}}">Resume</a>
                    <a class="text-primary-600" href="{{route('download.pdf', ['jobApplyDetail' => $details->id, 'type' => 'cover_letter'])}}">Cover letter</a>
                </p>
            @endif
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
                    stroke-dasharray="{{ $details?->percentage_fit ?? 0 }}, 100"
                    stroke="currentColor"
                    fill="none"
                    d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831"
                />
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-sm font-semibold">
                {{ $details?->percentage_fit ?? 0 }}%
            </span>
        </div>
    </div>
   {{-- <div class="bg-white shadow rounded-xl">
       <iframe 
            src="{{ route('resume.stream', ['user' => $user->id]) }}" 
            width="100%" 
            height="1200" 
            style="border:none;"
        ></iframe>
    </div>

     <div class="bg-white shadow rounded-xl">
       <iframe 
            src="{{ route('cover-letter.stream', ['user' => $user->id]) }}" 
            width="100%" 
            height="1200" 
            style="border:none;"
        ></iframe>
    </div> --}}
@endif