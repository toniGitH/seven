<?php

namespace App\Http\Controllers;

use App\Models\Roll;
use Illuminate\Support\Facades\Auth;

class RollController extends Controller
{
    
    public function store($id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $total = $dice1 + $dice2;
        $won = $total === 7 ? true : false;

        $roll= Roll::create([
            'dice1' => $dice1,
            'dice2' => $dice2,
            'won' => $won,
            'user_id' => $userId
        ]);

        return response([
            'message' =>ucfirst(Auth::user()->name) . '\'s roll executed correctly',
            'roll result' => $roll
        ], 201);
    }

    public function show($id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rolls = Roll::where('user_id', $userId)->get();

        if ($rolls->isEmpty()) {
            return response()->json(['message' => 'No rolls found for the user'], 404);
        }

        $totalRolls = Roll::where('user_id', $id)->count();
        $wonRolls = Roll::where('user_id', $id)->where('won', true)->count();
        $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;
        
        return response()->json([
            'message' => 'These are all the ' . ucfirst(Auth::user()->name) . '\'s rolls',
            'current success rate' => $winRate . '%',
            'rolls' => $rolls
        ], 200); 
    }

    public function destroy($id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rolls = Roll::where('user_id', $userId)->get();

        if ($rolls->isEmpty()) {
            return response()->json(['message' => 'No rolls found for the user'], 404);
        }

        Roll::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'All ' . ucfirst(Auth::user()->name) . '\'s rolls deleted successfully'
        ], 200);
    }

    public function getWinRate($id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $totalRolls = Roll::where('user_id', $id)->count();
        $wonRolls = Roll::where('user_id', $id)->where('won', true)->count();
        $winRate = $totalRolls > 0 ? ($wonRolls / $totalRolls) * 100 : 0;

        return response()->json([
            'user' => ucfirst(Auth::user()->name),
            'current success rate' => $winRate . '%'
        ], 200);
    }
}