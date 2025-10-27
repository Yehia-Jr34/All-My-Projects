<x-mail::message>
    Hello, {{$doctor_name}}!

    The patient {{$user_name}} has ANSWERED the Review for the week number: {{$week_number}}

    Best regards,
    {{ config('app.name') }}
</x-mail::message>
