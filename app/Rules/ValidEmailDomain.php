<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 'aol.com'];
        
        if (!str_contains($value, '@')) {
            $fail("Alamat email tidak valid.");
            return;
        }

        $domain = strtolower(substr(strrchr($value, "@"), 1));

        if (!in_array($domain, $allowedDomains)) {
            $fail("Domain email tidak valid. Gunakan domain seperti gmail.com, yahoo.com, dll.");
        }
    }

}
