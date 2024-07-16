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
        $fields = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000']
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
        $post = Post::findOrFail($id);
        if ($request->user('sanctum')->id !== $post->user->id)
            return response('forbidden', 403);
        $fields = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000']
        ]);
        $post->update($fields);
        return $post;
    }

    public function destroy(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        if ($request->user('sanctum')->id !== $post->user->id)
            return response('forbidden', 403);
        return Post::destroy($id);
    }

    public function getUserPosts(Request $request, string $userId){
        $user = User::findOrFail($userId);
        return $user->posts();
    }

}
