<x-mail::message>
    # Reset Your Password

    Click the button below to reset your password:

    <x-mail::button :url="$resetLink">
        Reset Password
    </x-mail::button>

    If you did not request a password reset, no further action is required.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
