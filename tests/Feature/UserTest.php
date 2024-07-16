<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_getting_current_user_without_credentials(): void
    {
        $response = $this->withHeader('accept', 'application/json')->get('/api/user');
        $response->assertStatus(401);
    }
}
