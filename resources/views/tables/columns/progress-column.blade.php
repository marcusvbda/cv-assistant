@php
    $value = $getState()
@endphp
@if(gettype($value) == 'string')
    <small class="text-xs opacity-50">{{ $value }}</small>
@else   
   <div class="flex flex-col gap-2 w-full py-2">
        @include("components.progress-bar",["percentage" => data_get($value,"percentage",0)])
        {!!  data_get($value,"links",0)  !!}
   </div>
@endif