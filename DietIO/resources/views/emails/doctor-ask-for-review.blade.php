<x-mail::message>
    Hello, {{$user_name}}!

    Dr. {{$doctor_name}} has asked for a Review for the week number: {{$week_number}}

    Best regards,
    {{ config('app.name') }}
</x-mail::message>
