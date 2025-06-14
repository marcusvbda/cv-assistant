@php
    $contaier = "display:flex;flex-direction:column;padding:40px;font-size:16px";
    $row = "display:flex;justify-content:space-between;align-items:center;";
    $col = "display:flex;flex-direction:column;gap:2px;flex:1;";
    $separator = '<hr style="margin:15px 0;border-color: #b0b0b0;"/>';

    $links = $user->links()->get();
    $phones = $user->phones()->get();
    $skills = $user->skills()->get();
    $courses = $user->courses()->get();
@endphp

<div style="{{ $contaier }}">
    <div style="{{ $row }} margin-bottom:40px;justify-content:center">
        <div style="{{ $col }} align-items:center">
            <strong style="font-size: 2rem;">{{ $user->name }}</strong>
            @if($user->position)
                <span style="font-size: 1rem;">{{ $user->position }}</span>
            @endif
        </div>
    </div>
    <div style="{{ $row }}">
        <div style="{{ $col }}">
            @if($links->count())
                @foreach($links as $link)
                    <span><strong>{{$link->name}} : </strong>{{ $link->value }}</span>
                @endforeach
            @endif
        </div>
        <div style="{{ $col }} text-align:right">
            <span><strong>Email : </strong> {{ $user->email }}</span>
            @if($phones->count())
                @foreach($phones as $phone)
                    <span><strong>Phone : </strong>{{ $phone->number }}</span>
                @endforeach
            @endif
        </div>
    </div>
    @if($user->introduction)
        {!! $separator !!}
        <div style="{{ $row }}justify-content:center;">
            <div style="{{ $col }} align-items:center">
                <strong style="font-size: 1.2rem;margin-bottom: 10px;">Introduction</strong>
                <div>{{ $user->introduction }}</div>
            </div>
        </div>
    @endif
    @if($skills->count())
        {!! $separator !!}
        <div style="{{ $row }}justify-content:center;">
            <div style="{{ $col }} align-items:center">
                <strong style="font-size: 1.2rem;margin-bottom: 10px;">Skills summary</strong>
            </div>
        </div>
        <div style="{{ $row }}">
           <div style="{{ $col }} gap:10px">
                @foreach($skills as $skill)
                    <li style="{{ $row }};align-items:center;gap:10px;justify-content:flex-start;">
                        <strong>{{ $skill->type }} :</strong>
                        <div>{{ implode(', ', $skill->value) }}</div>
                    </li>
                @endforeach
           </div>
        </div>
    @endif
    @if($courses->count())
        {!! $separator !!}
        <div style="{{ $row }}justify-content:center;">
            <div style="{{ $col }} align-items:center">
                <strong style="font-size: 1.2rem;margin-bottom: 10px;">Education</strong>
            </div>
        </div>
        <div style="{{ $row }}">
            <div style="{{ $col }} gap:10px">
                @foreach($courses as $course)
                   <div>
                        <div style="{{ $row }}">
                            <strong>{{ $course->instituition }}</strong>
                            <span>{{ $course->start_date->format('Y-m-d') }} - {{ !$course->end_date ? 'Present' : $course->end_date->format('Y-m-d') }}</span>
                        </div>
                        <div>{{ $course->name }}</div>
                   </div>
                @endforeach
           </div>
        </div>
    @endif
</div>