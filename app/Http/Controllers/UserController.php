<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index(){
        $user = Auth::user();
        $players = User::where('id', '!=', $user->id)->get();
        return response()->json([
            'players' => $players
        ]);
    }

    public function register(RegisterUserRequest $request)
    {
        $validatedData = $request->all();
        $validatedData['password'] = Hash::make($request->password);
        $user= User::create([
            'name'=>$request->name ? $request->name : 'anonimo',
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ]); // PENDIENTE DE ASIGACIÓN DE ROLES ->assignRole('Pplayer');
        $token = $user->createToken('authToken')->accessToken;
        return response([
            'message' => 'User ' . ucfirst($user->name) . ' registered successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 422);
        }
        $token = $user->createToken('authToken')->accessToken;
        return response([
            'message' => 'User ' . ucfirst($user->name) . ' logged successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout()
    {
        $user=Auth::user();
        $user->tokens->each->revoke();
        return response()->json([
            'message' => 'User ' . ucfirst($user->name) . ' logged out successfully'
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }
        $authUserId = Auth::user()->id;
        if ($authUserId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $user = Auth::user();
        User::where('id', $user->id)->update(['name'=>$request->name ? $request->name : 'anonimo']);
        return response()->json([
            'message' => 'Username updated successfully',
            'new name' => $request->name ? $request->name : 'anonimo'
        ]);
    }


}