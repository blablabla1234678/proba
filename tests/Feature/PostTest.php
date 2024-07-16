<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions;

    protected $user1 = [
        'name' => 'The Tester',
        'email' => 'test@example.com',
        'password' => 'abcd12345'
    ];
    protected $user2 = [
        'name' => 'The Other Tester',
        'email' => 'test2@example.com',
        'password' => 'ABCD12345'
    ];

    protected $post1 = [
        'title' => 'The Post',
        'body' => 'This the the post body. It should contain a long text.'
    ];
    protected $post1b = [
        'title' => 'The Post2',
        'body' => 'This the the post body2. It should contain a long text.'
    ];
    protected $post2 = [
        'title' => 'The Other Post',
        'body' => 'This the the other post body. It should contain a long text.'
    ];

    public function test_listing_posts_is_empty_when_no_posts_added():void{
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals($json, []);
    }

    public function test_listing_posts():void{
        $user = User::create($this->user1);
        Post::create(array_merge($this->post1, ['user_id' => $user->id]));
        Post::create(array_merge($this->post1b, ['user_id' => $user->id]));
        $response = $this->getJson('/api/posts');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonStructure([['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']]);
        $json = $response->json();
        $this->assertEquals(count($json), 2);
    }

    public function test_listing_user_posts():void{
        $user = User::create($this->user1);
        Post::create(array_merge($this->post1, ['user_id' => $user->id]));
        Post::create(array_merge($this->post1b, ['user_id' => $user->id]));
        $user2 = User::create($this->user2);
        Post::create(array_merge($this->post2, ['user_id' => $user2->id]));
        $response = $this->getJson('/api/users/'.$user->id.'/posts');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
        $response->assertJsonStructure([['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']]);
        $json = $response->json();
        $this->assertEquals(count($json), 2);
    }

    public function test_adding_post():void{
        $user1 = $this->getUser1IdAndToken();
        $response = $this->withToken($user1['token'])
            ->postJson('/api/posts', $this->post1);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']);
        $response->assertJson(array_merge($this->post1, [
            'user_id' => $user1['id']
        ]));
    }

    public function test_showing_post():void{
        $user = User::create($this->user1);
        $post = Post::create(array_merge($this->post1, ['user_id' => $user->id]));
        $response = $this->getJson('/api/posts/'.$post->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']);
        $json = $response->json();
        $this->assertEquals($post->title, $json['title']);
        $this->assertEquals($post->body, $json['body']);
    }

    public function test_updating_post():void{
        $user1 = $this->getUser1IdAndToken();
        $post = Post::create(array_merge($this->post1, ['user_id' => $user1['id']]));
        $response = $this->withToken($user1['token'])
            ->putJson('/api/posts/'.$post->id, $this->post1b);
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'user_id', 'title', 'body', 'updated_at', 'created_at']);
        $response->assertJson([
            'id' => $post->id,
            'title' => $this->post1b['title'],
            'body' => $this->post1b['body'],
        ]);
        $postInDb = Post::find($post->id);
        $this->assertEquals($postInDb->title, $this->post1b['title']);
        $this->assertEquals($postInDb->body, $this->post1b['body']);
    }

    public function test_deleting_post():void{
        $user1 = $this->getUser1IdAndToken();
        $post = Post::create(array_merge($this->post1, ['user_id' => $user1['id']]));
        $response = $this->withToken($user1['token'])
            ->deleteJson('/api/posts/'.$post->id);
        $response->assertStatus(200);
        $postInDb = Post::find($post->id);
        $this->assertEquals(!!$postInDb, false);
    }

    protected function getUser1IdAndToken(){
        $user = User::create($this->user1);
        $response = $this->postJson('/api/tokens', $this->user1);
        $response->assertStatus(201);
        $response->assertJsonStructure(['plainText']);
        $json = $response->json();
        return [
            'id' => $user->id,
            'token' => $json['plainText']
        ];
    }
}
