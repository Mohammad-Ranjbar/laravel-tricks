<?php

namespace Tests\Feature;

use App\Rules\ValidIranianNationalCode;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidIranianNationalCodeTest extends TestCase
{
    public function test_it_accepts_valid_national_codes_with_supported_digit_sets(): void
    {
        foreach (['0013547021', '۰۰۱۳۵۴۷۰۲۱', '٠٠١٣٥٤٧٠٢١'] as $nationalCode) {
            $validator = Validator::make(
                ['national_code' => $nationalCode],
                ['national_code' => ['required', 'string', new ValidIranianNationalCode]],
            );

            $this->assertTrue($validator->passes(), $nationalCode);
        }
    }

    public function test_it_rejects_invalid_national_codes(): void
    {
        foreach ([
            '0013547022',
            '۰۰۱۳۵۴۷۰۲۲',
            '1111111111',
            '123456789',
            '12345678901',
            'abcdefghij',
            13547021,
            '',
        ] as $nationalCode) {
            $validator = Validator::make(
                ['national_code' => $nationalCode],
                ['national_code' => ['required', 'string', new ValidIranianNationalCode]],
            );

            $this->assertTrue($validator->fails(), (string) $nationalCode);
        }
    }
}
