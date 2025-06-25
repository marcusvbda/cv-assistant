@php
    use App\Enums\JobDescriptionAnalysisStatusEnum;
@endphp
@if(!$user->hasAiIntegration())
    @include("filament.components.ai-not-configured")
@else
    <h1>{{JobDescriptionAnalysisStatusEnum::{$item->status}->description()}}</h1>
@endif