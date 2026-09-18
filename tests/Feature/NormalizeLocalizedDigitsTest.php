<?php

namespace Tests\Feature;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NormalizeLocalizedDigitsTest extends TestCase
{
    public function test_it_normalizes_persian_and_arabic_digits_in_nested_request_input(): void
    {
        Route::post('/_tests/normalize-digits', static fn (Request $request): JsonResponse => response()->json($request->all()));

        $response = $this->postJson('/_tests/normalize-digits?search=کاربر۱۲', [
            'mobile' => '۰۹۱۲۳۴۵۶۷۸۹',
            'national_code' => '٠٠١٣٥٤٧٠٢١',
            'address' => [
                'description' => 'طبقه ۲، واحد ٣',
            ],
            'count' => 12,
            'active' => true,
            'nullable' => null,
        ]);

        $response->assertOk()->assertExactJson([
            'search' => 'کاربر12',
            'mobile' => '09123456789',
            'national_code' => '0013547021',
            'address' => [
                'description' => 'طبقه 2، واحد 3',
            ],
            'count' => 12,
            'active' => true,
            'nullable' => null,
        ]);
    }

    public function test_it_does_not_mutate_passwords_or_opaque_security_values(): void
    {
        Route::post('/_tests/normalize-sensitive-values', static fn (Request $request): JsonResponse => response()->json($request->all()));

        $response = $this->postJson('/_tests/normalize-sensitive-values', [
            'password' => 'Secret۱۲۳',
            'password_confirmation' => 'Secret۱۲۳',
            'oauth' => [
                'client_secret' => 'secret۱۲۳',
                'refresh_token' => 'token۱۲۳',
            ],
            'otp' => '۱۲۳۴۵۶',
        ]);

        $response->assertOk()->assertExactJson([
            'password' => 'Secret۱۲۳',
            'password_confirmation' => 'Secret۱۲۳',
            'oauth' => [
                'client_secret' => 'secret۱۲۳',
                'refresh_token' => 'token۱۲۳',
            ],
            'otp' => '123456',
        ]);
    }
}
