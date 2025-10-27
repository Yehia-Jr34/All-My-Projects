<x-mail::message>
# Verifying email.

    Verify your email with us.

<x-mail::button :url="route('verification.verify', ['id' => $user->id, 'hash' => $user->email_verified_at])">
VERIFY
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
