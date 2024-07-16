<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }


    public function store(Request $request)
    {
        // only logged in
        $fields = $request->validate([
            'title' => ['required'],
            'body' => ['required']
        ]);
        $fields['user_id'] = $request->user()->id;
        return Post::create($fields);
    }

    public function show(string $id)
    {
        return Post::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        // only owner
        $post = Post::findOrFail($id);
        $fields = $request->validate([
            'title' => ['required'],
            'body' => ['required']
        ]);
        $post->update($fields);
        return $post;
    }

    public function destroy(string $id)
    {
        // only owner
        return Post::destroy($id);
    }

    public function getUserPosts(string $userId){
        $user = User::findOrFail($userId);
        return $user->posts();
    }

}
