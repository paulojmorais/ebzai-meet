@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
@php
$body = str_replace('[USER_NAME]',$meeting['user']['username'],$body);
$body = str_replace('[MEETING_ID]',$meeting['meeting_id'],$body);
$body = str_replace('[MEETING_TITLE]',$meeting['title'],$body);
$body = str_replace('[MEETING_PASSWORD]',$meeting['password'],$body);
$body = str_replace('[MEETING_DATE]',$meeting['date'],$body);
$body = str_replace('[MEETING_TITLE]',$meeting['time'],$body);
$body = str_replace('[MEETING_TIMEZONE]',$meeting['timezone'],$body);
$body = str_replace('[MEETING_DESCRIPTION]',$meeting['description'],$body);
@endphp
{!! @$body !!}
<!-- <p>{{ __('Greetings! :username has invited you to attend a virtual meeting', ['username' => $meeting['user']['username']]) }}
</p>

<ul>
<li><b>{{ __('Meeting ID') }}</b>: {{ $meeting['meeting_id'] }}</li>
<li><b>{{ __('Title') }}</b>: {{ $meeting['title'] }}</li>
<li><b>{{ __('Password') }}</b>: {{ $meeting['password'] ? $meeting['password'] : '-' }}</li>
<li><b>{{ __('Date') }}</b>: {{ $meeting['date'] ? formatDate($meeting['date']) : '-' }}</li>
<li><b>{{ __('Time') }}</b>: {{ $meeting['time'] ? formatTime($meeting['time']) : '-' }}</li>
<li><b>{{ __('Timezone') }}</b>: {{ $meeting['timezone'] ? $meeting['timezone'] : '-' }}</li>
<li><b>{{ __('Description') }}</b>: {{ $meeting['description'] ? $meeting['description'] : '-' }}</li>
</ul> -->

@component('mail::button', ['url' => Request::root() . '/meeting/' . $meeting['meeting_id']])
{{ __('Join Now') }}
@endcomponent

<!-- <p>{{ __('Thank you') }}</p> -->

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
