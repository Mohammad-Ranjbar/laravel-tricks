<?php

namespace App\Http\Middleware;

use App\Support\DigitNormalizer;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Override;

final class NormalizeLocalizedDigits extends TransformsRequest
{
    private const array SENSITIVE_FIELDS = [
        'authorization_code',
        'client_secret',
        'code_challenge',
        'code_verifier',
        'current_password',
        'hash',
        'password',
        'password_confirmation',
        'secret',
        'signature',
        'token',
    ];

    #[Override]
    protected function transform(mixed $key, mixed $value): mixed
    {
        if (! is_string($value) || $this->isSensitive($key)) {
            return $value;
        }

        return DigitNormalizer::toEnglish($value);
    }

    private function isSensitive(string $key): bool
    {
        $field = str_contains($key, '.')
            ? substr((string) strrchr($key, '.'), 1)
            : $key;

        return in_array($field, self::SENSITIVE_FIELDS, true)
            || str_ends_with($field, '_secret')
            || str_ends_with($field, '_signature')
            || str_ends_with($field, '_token');
    }
}
