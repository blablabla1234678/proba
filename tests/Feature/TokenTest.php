<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TokenTest extends TestCase
{
    use DatabaseTransactions;

    protected $user1 = [
        'name' => 'The Tester',
        'email' => 'test@example.com',
        'password' => 'abcd12345'
    ];

    public function test_creating_token():void{
        $user = User::create($this->user1);
        $response = $this->postJson('/api/tokens', $this->user1);
        $response->assertStatus(201);
        $response->assertJsonStructure(['plainText']);
    }

    public function test_deleting_token():void{
        $user = User::create($this->user1);
        $response = $this->postJson('/api/tokens', $this->user1);
        $response->assertStatus(201);
        $response->assertJsonStructure(['plainText']);
        $token = $response->json();
        $response = $this->withToken($token['plainText'])
            ->deleteJson('/api/tokens/current');
        $response->assertStatus(204);
        //$response = $this->withToken($token['plainText'])
        //    ->deleteJson('/api/tokens/current');
        //$response->assertStatus(401);

        //TODO
        //Sanctum does not want to logout for some reason
    }
}
