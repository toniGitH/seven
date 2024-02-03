<?php

namespace App\Http\Controllers;

use App\Models\Roll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RollController extends Controller
{
    
    public function index($id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $rolls = Roll::where('user_id', $userId)->get();
    
        return response()->json([
            'message' => 'These are all the ' . Auth::user()->name . '\'s rolls',
            'rolls' => $rolls
        ], 200); 
    }
    
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
            'message' => Auth::user()->name . '\'s roll executed correctly',
            'roll result' => $roll
        ], 201);
    }

    public function show(Roll $roll)
    {
        //
    }

    public function update(Request $request, Roll $roll)
    {
        //
    }

    public function destroy($id)
    {
        $userId = Auth::user()->id;
        if ($userId != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Roll::where('user_id', $userId)->delete();
        
        return response()->json(['message' => 'All ' . Auth::user()->name . '\'s rolls deleted successfully'], 200);
    }

}
