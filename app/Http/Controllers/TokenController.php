<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    public function store(Request $request){
        // only guest
        $fields = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255']
        ]);
        $user = User::where('email', $fields['email'])->first();
        if (!$user || !Hash::check($fields['password'], $user->password))
            return response('authentication failed', 401);

        $token = $user->createToken('the_token');
        return response([
            'plainText' => $token->plainTextToken
        ], 201);
    }

    public function destroy(Request $request){
        // only logged in
        $user = $request->user();
        $user->tokens()->delete();
        return response(null, 204);
    }
}
