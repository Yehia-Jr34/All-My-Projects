<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DateTimeRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Validate the date and time format (e.g., Y-m-d H:i:s)
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value);
    }

    public function message(): string
    {
        return 'The :attribute must be a valid date and time in the format Y-m-d H:i:s.';
    }
}
