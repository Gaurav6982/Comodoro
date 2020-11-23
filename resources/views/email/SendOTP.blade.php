@component('mail::message')
# Verification

{{-- The body of your message. --}}

Your OTP is {{$data}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
