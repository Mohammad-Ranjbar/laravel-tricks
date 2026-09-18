<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
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

    public function test_backend_status_route_uses_the_stateless_api_middleware_group(): void
    {
        $middleware = Route::getRoutes()->getByName('home')?->gatherMiddleware();

        $this->assertContains('api', $middleware);
        $this->assertNotContains('web', $middleware);
    }
}
