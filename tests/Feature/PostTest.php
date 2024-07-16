<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    public function test_listing_posts_is_empty_when_no_posts_added():void {
        $response = $this->getJson('/api/posts')
            ->assertStatus(200);
        $this->assertEquals($response->json(), []);
    }

    public function test_listing_posts():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $container->createPost($data->post1);
        $container->createPost($data->post1b);
        $response = $this->getJson('/api/posts')
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonStructure([['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']]);
        $this->assertEquals(count($response->json()), 2);
    }

    public function test_listing_user_posts():void {
        $container = new Container();
        $data = new Data();
        $user = $container->createUser($data->user1);
        $container->createPost($data->post1);
        $container->createPost($data->post1b);
        $container->createUser($data->user2);
        $container->createPost($data->post2);
        $this->assertEquals($container->countUsers(), 2);
        $this->assertEquals($container->countPosts(), 3);
        $response = $this->getJson('/api/users/'.$user->id.'/posts')
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonStructure([['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']]);
        $this->assertEquals(count($response->json()), 2);
    }

    public function test_adding_post():void {
        $container = new Container();
        $data = new Data();
        $user = $container->createUser($data->user1);
        $this->withToken($container->createToken())
            ->postJson('/api/posts', $data->post1)
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'user_id', 'title', 'body', 'updated_at', 'created_at'])
            ->assertJson($data->post1)
            ->assertJson(['user_id' => $user->id]);
    }

    public function test_showing_post():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $post = $container->createPost($data->post1);
        $this->getJson('/api/posts/'.$post->id)
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'user_id', 'title', 'body', 'updated_at', 'created_at'])
            ->assertJson($data->post1);
    }

    public function test_updating_post():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $post = $container->createPost($data->post1);
        $this->withToken($container->createToken())
            ->putJson('/api/posts/'.$post->id, $data->post1b)
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'user_id', 'title', 'body', 'updated_at', 'created_at'])
            ->assertJson(['id' => $post->id])
            ->assertJson($data->post1b);
        $this->assertEquals($container->getPostData($post->id), $data->post1b);
    }

    public function test_deleting_post():void {
        $container = new Container();
        $data = new Data();
        $container->createUser($data->user1);
        $post = $container->createPost($data->post1);
        $this->assertEquals($container->countPosts(), 1);
        $this->withToken($container->createToken())
            ->deleteJson('/api/posts/'.$post->id)
            ->assertStatus(200);
        $this->assertEquals($container->countPosts(), 0);
    }
}
