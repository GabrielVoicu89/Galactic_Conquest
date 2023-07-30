@component('mail::message')
    # Introduction

    You asked for it {{ config('app.name') }} to the rescue.

    @component('mail::button', ['url' => 'http://localhost:5173/ResetPassword/' . $user->remember_token])
        Reset Password
    @endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent
