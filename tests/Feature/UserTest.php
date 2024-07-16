<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    protected $user0 = [
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => 'sdlghn3w9rzoowehgln'
    ];

    protected $user1 = [
        'name' => 'The Tester',
        'email' => 'test@example.com',
        'password' => 'abcd12345'
    ];
    protected $user1b = [
        'name' => 'The Tester2',
        'email' => 'test2@example.com',
        'password' => 'abcd1234567'
    ];

    public function test_getting_current_user_without_credentials(): void
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }

    public function test_listing_users_is_empty_at_beginning():void{
        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonStructure([]);
    }

    public function test_listing_users_returns_array_when_database_is_seeded():void{
        User::create($this->user0);
        User::create($this->user1);
        User::create($this->user1b);
        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonStructure([['id', 'name', 'email', 'updated_at', 'created_at']]);
        $json = $response->json();
        $this->assertEquals(count($json), 3);
    }

    public function test_adding_user():void{
        $response = $this->postJson('/api/users', $this->user1);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at']);
        $response->assertJson([
            'name' => $this->user1['name'],
            'email' => $this->user1['email']
        ]);
    }

    public function test_showing_user():void{
        $response = $this->postJson('/api/users', $this->user1);
        $json = $response->json();
        $response = $this->getJson('/api/users/'.$json['id']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at']);
    }

    public function test_updating_user():void{
        $response = $this->postJson('/api/users', $this->user1);
        $json = $response->json();
        $response = $this->putJson('/api/users/'.$json['id'], $this->user1b);
        $response->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at']);
        $response->assertJson([
            'id' => $json['id'],
            'name' => $this->user1b['name']
        ]);
    }

    public function test_deleting_user():void{
        $response = $this->postJson('/api/users', [
            'name' => 'The Tester',
            'email' => 'test@example.com',
            'password' => 'abcd12345'
        ]);
        $json = $response->json();
        $this->deleteJson('/api/users/'.$json['id']);
        $response = $this->getJson('/api/users/'.$json['id']);
        $response->assertStatus(404);
    }
}
