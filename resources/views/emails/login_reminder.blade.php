{{-- <x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> --}}

@component('mail::message')
# Hello {{ $user->name }},

We noticed you haven’t logged in for a while, and we just want to say: *We miss you!* 

Come back and check out what’s new at our Hotel!

@component('mail::button', ['url' => url('http://127.0.0.1:8000/login')])
Login Now
@endcomponent

Thanks,<br>
The Team
@endcomponent


{{-- @component('mail::message')
# We Miss You!

Hello {{ $user->name }},

We noticed you haven't logged in to your account for over a month. We'd love to have you back!

@component('mail::button', ['url' => route('login')])
Login Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent --}}