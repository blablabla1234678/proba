<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required']
        ]);
        return User::create($fields);
    }

    public function show(string $id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $fields = $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required']
        ]);
        $user->update($fields);
        return $user;
    }

    public function destroy(string $id)
    {
        return User::destroy($id);
    }
}
