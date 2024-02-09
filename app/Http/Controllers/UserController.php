<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roll;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class UserController extends Controller
{
    use HasRoles;
    
    public function register(RegisterUserRequest $request)
    {
        $validatedData = $request->all();
        $validatedData['password'] = Hash::make($request->password);
        $user= User::create([
            'name'=>$request->name ? $request->name : 'anonimo',
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ])->assignRole('Player');
        $token = $user->createToken('authToken')->accessToken;
        return response([
            'message' => 'User ' . ucfirst($user->name) . ' registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
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
        ], 200);
    }

    public function logout()
    {
        $user=Auth::user();
        $user->tokens->each->revoke();
        return response()->json([
            'message' => 'User ' . ucfirst($user->name) . ' logged out successfully'
        ], 200);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json([
                'error' => 'Unauthorized',
                'warning' => 'You don\'t have permission to update another user\'s names '
            ], 403);
        }
        User::where('id', $userId)->update(['name'=>$request->name ? $request->name : 'anonimo']);
        return response()->json([
            'message' => 'Username updated successfully',
            'new name' => $request->name ? $request->name : 'anonimo'
        ], 200);
    }

    private function getPlayerWinRates()
    {
        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();

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
        return $playersWithWinRate;
    }

    public function index()
    {
        $playersWithWinRate = $this->getPlayerWinRates();

        if (empty($playersWithWinRate)) {
            return response()->json([
                'message' => 'No players found yet.',
                'players' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Players list with succes rate',
            'players' => $playersWithWinRate
        ], 200);
    }

    public function ranking()
    {
        $playersWithWinRate = $this->getPlayerWinRates();

        if (empty($playersWithWinRate)) {
            return response()->json([
                'message' => 'No players found yet.',
                'players' => []
            ], 404);
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
        ], 200);
    }

    public function winner()
    {
        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();

        if ($players->isEmpty()) {
            return response()->json([
                'message' => 'No players found yet.',
                'players' => []
            ], 404);
        }

        $maxWinRate = 0;
        $winner = null;

        foreach ($players as $player) {
            $totalRolls = Roll::where('user_id', $player->id)->count();
            $wonRolls = Roll::where('user_id', $player->id)->where('won', true)->count();
            $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;

            if ($winRate > $maxWinRate) {
                $maxWinRate = $winRate;
                $winners = [
                    [
                        'user' => ucfirst($player->name),
                        'win_rate' => $winRate . '%'
                    ]
                ];
            } elseif ($winRate == $maxWinRate) {
                $winners[] = [
                    'user' => ucfirst($player->name),
                    'win_rate' => $winRate . '%'
                ];
            }
        }

        return response()->json([
            'message' => 'Player or players with the highest win rate',
            'winner' => $winners
        ], 200);
    }

    public function loser()
    {
        $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })->get();

        if ($players->isEmpty()) {
            return response()->json([
                'message' => 'No players found yet.',
                'players' => []
            ], 404);
        }
        
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
        ], 200);
    }

}