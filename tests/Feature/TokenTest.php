<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TokenTest extends TestCase
{
    use DatabaseTransactions;

    public function test_creating_token():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $this->assertEquals($container->countTokens(), 0);
        $this->postJson('/api/tokens', $data->user1)
            ->assertStatus(201)
            ->assertJsonStructure(['plainText']);
        $this->assertEquals($container->countTokens(), 1);
    }

    public function test_deleting_token():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $token = $container->createToken();
        $this->assertEquals($container->countTokens(), 1);
        $this->withToken($token)
            ->deleteJson('/api/tokens/current')
            ->assertStatus(204);
        $this->assertEquals($container->countTokens(), 0);
    }
}