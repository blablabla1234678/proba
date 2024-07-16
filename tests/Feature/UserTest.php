<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listing_users_is_empty_when_no_users_added():void{
        $response = $this->getJson('/api/users')
            ->assertStatus(200)
            ->assertJsonIsArray();
        $this->assertEquals($response->json(), []);
    }

    public function test_listing_users():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $container->createUser($data->user1b);
        $response = $this->getJson('/api/users')
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonStructure([['id', 'name', 'email', 'updated_at', 'created_at']]);
        $this->assertEquals(count($response->json()), 2);
    }

    public function test_adding_user():void {
        $container = new Container();
        $data = new Data();
        $response = $this->postJson('/api/users', $data->user1)
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at'])
            ->assertJson($data->user1_public);
        $id = $response->json()['id'];
        $this->assertEquals($container->getUserData($id), $data->user1_public);
    }

    public function test_showing_user():void {
        $container = new Container();
        $data = new Data();
        $user = $container->createUser($data->user1);
        $this->getJson('/api/users/'.$user->id)
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at'])
            ->assertJson($data->user1_public);
    }

    public function test_updating_user():void {
        $container = new Container();
        $data = new Data();
        $user = $container->createUser($data->user1);
        $this->withToken($container->createToken())
            ->putJson('/api/users/'.$user->id, $data->user1b)
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at'])
            ->assertJson(['id' => $user->id])
            ->assertJson($data->user1b_public);
        $this->assertEquals($container->getUserData($user->id), $data->user1b_public);
    }

    public function test_deleting_user():void {
        $container = new Container();
        $data = new Data();
        $user = $container->createUser($data->user1);
        $this->assertEquals($container->countUsers(), 1);
        $this->withToken($container->createToken())
            ->deleteJson('/api/users/'.$user->id)
            ->assertStatus(200);
        $this->assertEquals($container->countUsers(), 0);
    }

}
