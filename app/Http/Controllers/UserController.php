<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $fields['password'] = Hash::make($fields['password']);
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
        $fields['password'] = Hash::make($fields['password']);
        $user->update($fields);
        return $user;
    }

    public function destroy(string $id)
    {
        return User::destroy($id);
    }
}
