<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_core_fields_and_dynamic_profile_attributes_are_persisted(): void
    {
        $user = User::factory()->create([
            'mobile' => '+989121234567',
            'username' => 'mohammad',
            'national_code' => '0013547021',
            'password' => 'secret-password',
        ]);

        $user->profile()->create([
            'attributes' => [
                'birth_date' => '1990-05-20',
                'job_title' => 'backend_developer',
            ],
        ]);

        $user->refresh()->load('profile');

        $this->assertSame('+989121234567', $user->mobile);
        $this->assertSame('0013547021', $user->national_code);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertSame('argon2id', Hash::info($user->password)['algoName']);
        $this->assertSame('backend_developer', $user->profile?->attributes['job_title']);
    }

    public function test_user_can_be_soft_deleted_without_deleting_its_profile(): void
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create([
            'attributes' => ['job_title' => 'backend_developer'],
        ]);

        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertNull(User::query()->find($user->id));
        $this->assertDatabaseHas('user_profiles', ['user_id' => $profile->user_id]);
    }
}
