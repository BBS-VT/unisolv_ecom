<?php

namespace App\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;
class CommaSeparatedEmails implements Rule
{
    protected $invalidEmails = [];

    public function passes($attibute, $value)
    {
        if (empty($value)) {
            return true;
        }

        $emails = array_map('trim', explode(',', $value));
        $this->invalidEmails = [];

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->invalidEmails[] = $email;
            }
        }

        return empty($this->invalidEmails);
    }

    public function message()
    {
        return 'The following email addresses are invalid: ' . implode(', ', $this->invalidEmails);
    }
}
