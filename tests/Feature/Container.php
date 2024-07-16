<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;

class Container {

    protected $user;
    protected $token;
    protected $post;

    public function createUser(array $data):User {
        $this->user = User::create($data);
        return $this->user;
    }

    public function getUserData(string $id):array {
        $user = User::find($id);
        return [
            'name' => $user->name,
            'email' => $user->email
        ];
    }

    public function countUsers():int {
        return User::all()->count();
    }

    public function lastUser():User {
        return $this->user;
    }

    public function createToken(User $user = null):string {
        if (!isset($user))
            $user = $this->lastUser();
        $this->token = $user->createToken('the_token')->plainTextToken;
        return $this->token;
    }

    public function countTokens(User $user = null):int {
        if (!isset($user))
            $user = $this->lastUser();
        return $user->tokens()->get()->count();
    }

    public function lastToken():string {
        return $this->token;
    }

    public function createPost(array $data, User $user = null):Post {
        if (!isset($user))
            $user = $this->lastUser();
        $this->post = Post::create(array_merge($data, ['user_id' => $user->id]));
        return $this->post;
    }

    public function getPostData(string $id):array {
        $post = Post::find($id);
        return [
            'title' => $post->title,
            'body' => $post->body
        ];
    }

    public function countPosts():int {
        return Post::all()->count();
    }

    public function lastPost():Post {
        return $this->post;
    }
}