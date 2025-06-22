@php
    $user = $user->load(['skills', 'experiences']);
    $skills = $user->skills()->get();
    $experiences = $user->experiences()->get();
    $date = \Carbon\Carbon::now()->format('F j, Y');
    $phones = $user->phones()->get();

    function skillList($skills) {
        return $skills->map(fn($s) => implode(', ', $s->value))->implode(', ');
    }

    function firstExperience($experiences) {
        return $experiences->sortByDesc('start_date')->first();
    }

    function cleanUrl($url) {
        return str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $url)) );
    }
@endphp

<table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <tr>
        <td align="right" colspan="2">{{ $date }}</td>
    </tr>
    <tr>
        <td colspan="2" style="padding-top: 30px;">
            <strong>{{ $user->name }}</strong><br>
            @foreach($phones as $phone)
                {{ $phone->number }}<br>
            @endforeach
            {{ $user->email }}<br>
            @if($user->linkedin)
                {{ cleanUrl($user->linkedin) }}<br>
            @endif
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding-top: 40px;">
            Dear Hiring Manager,
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding-top: 20px;">
            I'm writing to express my interest in the open position at your company. As a passionate and experienced professional in the field of {{ $user->position ?? 'technology' }}, I bring a strong background in areas such as {{ skillList($skills) }}.
        </td>
    </tr>

    @if($experiences->count())
        @php $exp = firstExperience($experiences); @endphp
        <tr>
            <td colspan="2" style="padding-top: 20px;">
                In my most recent role as <strong>{{ $exp->position }}</strong> at <strong>{{ $exp->company }}</strong>, I {{ strtolower(Str::limit($exp->description, 250)) }}.
            </td>
        </tr>
    @endif

    <tr>
        <td colspan="2" style="padding-top: 20px;">
            I am highly motivated to bring my skills and dedication to a team that values innovation and continuous growth. I believe I would be a valuable addition to your organization and am eager to contribute meaningfully to your projects and goals.
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding-top: 20px;">
            Thank you for considering my application. I would welcome the opportunity to discuss how I can contribute to your team. Please feel free to reach out at your convenience.
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding-top: 30px;">
            Sincerely,<br><br>
            <strong>{{ $user->name }}</strong>
        </td>
    </tr>
</table>
