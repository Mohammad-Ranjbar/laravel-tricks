<?php

namespace App\Rules;

use App\Support\DigitNormalizer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final class ValidIranianNationalCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid Iranian national code.');

            return;
        }

        $nationalCode = DigitNormalizer::toEnglish($value);

        if (preg_match('/^[0-9]{10}$/', $nationalCode) !== 1
            || preg_match('/^([0-9])\1{9}$/', $nationalCode) === 1) {
            $fail('The :attribute must be a valid Iranian national code.');

            return;
        }

        $sum = 0;

        for ($position = 0; $position < 9; $position++) {
            $sum += (int) $nationalCode[$position] * (10 - $position);
        }

        $remainder = $sum % 11;
        $expectedCheckDigit = $remainder < 2 ? $remainder : 11 - $remainder;

        if ((int) $nationalCode[9] !== $expectedCheckDigit) {
            $fail('The :attribute must be a valid Iranian national code.');
        }
    }
}
