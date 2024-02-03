<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roll;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $players = User::where('id', '!=', $user->id)->get();

        $playersWithWinRate = [];

        foreach ($players as $player) {
            $totalRolls = Roll::where('user_id', $player->id)->count();
            $wonRolls = Roll::where('user_id', $player->id)->where('won', true)->count();
            $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;

            $playersWithWinRate[] = [
                'user' => ucfirst($player->name),
                'win_rate' => $winRate . '%'
            ];
        }

        return response()->json([
            'message' => 'Players list with succes rate',
            'players' => $playersWithWinRate
        ]);
    }

    public function ranking()
    {
        $user = Auth::user();
        $players = User::where('id', '!=', $user->id)->get();

        $playersWithWinRate = [];

        foreach ($players as $player) {
            $totalRolls = Roll::where('user_id', $player->id)->count();
            $wonRolls = Roll::where('user_id', $player->id)->where('won', true)->count();
            $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;

            $playersWithWinRate[] = [
                'user' => ucfirst($player->name),
                'win_rate' => $winRate 
            ];
        }

        usort($playersWithWinRate, function ($a, $b) {
            return $b['win_rate'] - $a['win_rate'];
        });

        foreach ($playersWithWinRate as &$player) {
            $player['win_rate'] .= '%';
        }

        $rank = 1;
        foreach ($playersWithWinRate as &$player) {
            $player['rank'] = $rank . 'º';
            $rank++;
        }

        return response()->json([
            'message' => 'Player ranking by win rate',
            'players' => $playersWithWinRate
        ]);
    }

    public function winner()
    {
        $user = Auth::user();
        $players = User::where('id', '!=', $user->id)->get();
        $maxWinRate = 0;
        $winner = null;

        foreach ($players as $player) {
            $totalRolls = Roll::where('user_id', $player->id)->count();
            $wonRolls = Roll::where('user_id', $player->id)->where('won', true)->count();
            $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;

            if ($winRate > $maxWinRate) {
                $maxWinRate = $winRate;
                $winner = [
                    'user' => ucfirst($player->name),
                    'win_rate' => $winRate . '%'
                ];
            }
        }

        return response()->json([
            'message' => 'Player with the highest win rate',
            'winner' => $winner
        ]);
    }

    public function loser()
    {
        $user = Auth::user();
        $players = User::where('id', '!=', $user->id)->get();

        $minWinRate = PHP_INT_MAX;
        $loser = null;

        foreach ($players as $player) {
            $totalRolls = Roll::where('user_id', $player->id)->count();
            $wonRolls = Roll::where('user_id', $player->id)->where('won', true)->count();
            $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;

            if ($winRate < $minWinRate) {
                $minWinRate = $winRate;
                $loser = [
                    'user' => ucfirst($player->name),
                    'win_rate' => $winRate . '%'
                ];
            }
        }

        return response()->json([
            'message' => 'Player with the lowest win rate',
            'loser' => $loser
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
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        User::where('id', $userId)->update(['name'=>$request->name ? $request->name : 'anonimo']);
        return response()->json([
            'message' => 'Username updated successfully',
            'new name' => $request->name ? $request->name : 'anonimo'
        ]);
    }


}