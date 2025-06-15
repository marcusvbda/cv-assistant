@php
    $user = $user->load(['links', 'phones', 'skills', 'courses']);
    $links = $user->links()->get();
    $phones = $user->phones()->get();
    $skills = $user->skills()->get();
    $courses = $user->courses()->get();
    $experiences = $user->experiences()->get();
    $projects = $user->projects()->get();
    $certificates = $user->certificates()->get();
    $hr = '<tr><td colspan="2"><hr style="margin:20px 0;border-color:#b0b0b0;"></td></tr>';
    $header =fn($title) => '<tr>
            <td colspan="2" align="center">
                <div style="font-size:1.2rem; font-weight:bold; margin-bottom:20px;">'.$title.'</div>
            </td>
        </tr>';
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px; padding:40px; width:100%;">
    <tr>
        <td colspan="2" align="center" style="padding-bottom:50px;">
            <div style="font-size:2rem; font-weight:bold;">{{ $user->name }}</div>
            @if($user->position)
                <div style="font-size:1rem;">{{ $user->position }}</div>
            @endif
        </td>
    </tr>
    <tr>
        @if(count($links))
            <td valign="top" style="padding-right:20px;">
               <table>
                    @foreach($links as $link)
                        <tr>
                            <td><strong>{{ $link->name }}:</strong></td>
                            <td style="padding-left: 10px">{{ $link->value }}</td>
                        </tr>
                    @endforeach
               </table>
            </td>
        @endif
        <td valign="top" align="right">
            <table>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td style="padding-left: 10px">{{ $user->email }}</td>
                </tr>
                @foreach($phones as $phone)
                    <tr>
                        <td><strong>{{ $phone->type }}:</strong></td>
                        <td style="padding-left: 10px;text-align:right">{{ $phone->number }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
    @if($user->introduction)
        {!! $hr !!}
        <tr>
            <td colspan="2" align="center">
                <div style="font-size:1.2rem; font-weight:bold; margin-bottom:20px;">Introduction</div>
                <div style="text-align:left; margin-bottom:20px;">{{ $user->introduction }}</div>
            </td>
        </tr>
    @endif
    @if($skills->count())
        {!! $hr !!}
        {!! $header('Skills Summary') !!}
        @foreach($skills as $skill)
            <tr>
                <td colspan="2" style="padding-bottom:6px;">
                    <table>
                        <tr>
                            <td><strong>{{ $skill->type }}:</strong></td>
                            <td style="padding-left: 10px">{{ implode(', ', $skill->value) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    @endif
    @if($courses->count())
        {!! $hr !!}
        {!! $header('Education') !!}
        @foreach($courses as $course)
            <tr>
                <td colspan="2" style="padding-bottom:10px;">
                    <table width="100%">
                        <tr>
                            <td width="70%" style="font-weight:bold;">{{ $course->instituition }}</td>
                            <td align="right">
                                {{ $course->start_date->format('M Y') }} | 
                                {{ !$course->end_date ? 'Present' : $course->end_date->format('M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $course->name }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    @endif
    @if($experiences->count())
        {!! $hr !!}
        {!! $header('Work Experience') !!}
        @foreach($experiences as $experience)
            <tr>
                <td colspan="2" style="padding-bottom:10px;">
                    <table width="100%">
                        <tr>
                            <td width="70%" style="font-weight:bold;">{{ $experience->position }} | {{ $experience->company }}</td>
                            <td align="right">
                                {{ $experience->start_date->format('M Y') }} | 
                                {{ !$experience->end_date ? 'Present' : $experience->end_date->format('M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $experience->description }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    @endif
    @if($projects->count())
        {!! $hr !!}
        {!! $header('Projects') !!}
        @foreach($projects as $project)
            <tr>
                <td colspan="2" style="padding-bottom:10px;">
                    <table width="100%">
                        <tr>
                            <td width="70%" style="font-weight:bold;">{{ $project->name }}</td>
                            <td align="right">
                                {{ $project->start_date->format('M Y') }} | 
                                {{ !$project->end_date ? 'Present' : $project->end_date->format('M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $project->description }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    @endif
    @if($certificates->count())
        {!! $hr !!}
        {!! $header('Certificates') !!}
        @foreach($certificates as $certificate)
            <tr>
                <td colspan="2" style="padding-bottom:10px;">
                    <table width="100%">
                        <tr>
                            <td width="70%" style="font-weight:bold;">{{ $certificate->name }}</td>
                            <td align="right">
                                {{ $certificate->date->format('M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $certificate->description }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    @endif
</table>
