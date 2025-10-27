<x-mail::message>
    # Welcome

    As an Admin with us

    Your account is :
        'username' = {{$username}}
        'email' = {{$email}}
        'password' = {{$password}}

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
