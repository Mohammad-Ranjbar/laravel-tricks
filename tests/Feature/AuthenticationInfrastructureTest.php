<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthenticationInfrastructureTest extends TestCase
{
    public function test_passport_is_the_default_api_authentication_guard(): void
    {
        $this->assertSame('api', config('auth.defaults.guard'));
        $this->assertSame('passport', config('auth.guards.api.driver'));
        $this->assertInstanceOf(OAuthenticatable::class, new User);
    }

    public function test_access_and_refresh_token_lifetimes_are_bounded(): void
    {
        $this->assertSame(15, Passport::tokensExpireIn()->i);
        $this->assertSame(0, Passport::tokensExpireIn()->h);
        $this->assertSame(30, Passport::refreshTokensExpireIn()->d);
    }

    public function test_queue_batches_use_postgresql_metadata_while_jobs_use_redis(): void
    {
        $this->assertSame('queue', config('queue.connections.redis.connection'));
        $this->assertSame('job_batches', config('queue.batching.table'));
        $this->assertSame(config('database.default'), config('queue.batching.database'));
    }
}
