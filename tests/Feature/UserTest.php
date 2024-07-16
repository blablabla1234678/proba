<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_listing_users_is_empty_when_no_users_added():void{
        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals($json, []);
    }

    public function test_listing_users():void{
        User::create($this->user1);
        User::create($this->user1b);
        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonStructure([['id', 'name', 'email', 'updated_at', 'created_at']]);
        $json = $response->json();
        $this->assertEquals(count($json), 2);
    }

    public function test_adding_user():void{
        $response = $this->postJson('/api/users', $this->user1);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at']);
        $json = $response->json();
        $response->assertJson([
            'name' => $this->user1['name'],
            'email' => $this->user1['email']
        ]);
        $userInDb = User::find($json['id']);
        $this->assertEquals(!!$userInDb, true);
        $this->assertEquals($userInDb->name, $json['name']);
        $this->assertEquals($userInDb->email, $json['email']);
    }

    public function test_showing_user():void{
        $user = User::create($this->user1);
        $response = $this->getJson('/api/users/'.$user->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at']);
        $json = $response->json();
        $this->assertEquals($user->name, $json['name']);
        $this->assertEquals($user->email, $json['email']);
    }

    public function test_updating_user():void{
        $user = User::create($this->user1);
        $response = $this->putJson('/api/users/'.$user->id, $this->user1b);
        $response->assertJsonStructure(['id', 'name', 'email', 'updated_at', 'created_at']);
        $response->assertJson([
            'id' => $user->id,
            'name' => $this->user1b['name'],
            'email' => $this->user1b['email'],
        ]);
        $userInDb = User::find($user->id);
        $this->assertEquals($userInDb->name, $this->user1b['name']);
        $this->assertEquals($userInDb->email, $this->user1b['email']);
    }

    public function test_deleting_user():void{
        $user = User::create($this->user1);
        $this->deleteJson('/api/users/'.$user->id);
        $userInDb = User::find($user->id);
        $this->assertEquals(!!$userInDb, false);
    }
}
