<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\{
    LoginRequest,
    StoreUserRequest
};


class UserController extends Controller
{
    
    public function register(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return response()->json(['status' => 201 , 'message' => 'record saved!']);
    }
    
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }
        $user = User::where('email', $validated['email'])->firstOrFail();
        $user['token'] = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['status' => 201 , 'data' => UserResource::make($user)]);
    }
}
