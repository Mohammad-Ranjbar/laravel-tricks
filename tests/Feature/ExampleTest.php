<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_returns_the_backend_status(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertJson([
                'name' => config('app.name'),
                'status' => 'ok',
            ]);
    }
}
