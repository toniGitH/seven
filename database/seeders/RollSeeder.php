<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roll;

class RollSeeder extends Seeder
{
    
    public function run(): void
    {
        // User 3 rolls
        Roll::create([
            'dice1' => 1,
            'dice2' => 3,
            'won' => false,
            'user_id' => 3
        ]);
        Roll::create([
            'dice1' => 1,
            'dice2' => 4,
            'won' => false,
            'user_id' => 3
        ]);
        Roll::create([
            'dice1' => 2,
            'dice2' => 2,
            'won' => false,
            'user_id' => 3
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 3,
            'won' => false,
            'user_id' => 3
        ]);
        Roll::create([
            'dice1' => 4,
            'dice2' => 4,
            'won' => false,
            'user_id' => 3
        ]);
        // User 4 rolls
        Roll::create([
            'dice1' => 4,
            'dice2' => 3,
            'won' => true,
            'user_id' => 4
        ]);
        Roll::create([
            'dice1' => 3,
            'dice2' => 4,
            'won' => true,
            'user_id' => 4
        ]);
        Roll::create([
            'dice1' => 2,
            'dice2' => 2,
            'won' => false,
            'user_id' => 4
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 3,
            'won' => false,
            'user_id' => 4
        ]);
        Roll::create([
            'dice1' => 4,
            'dice2' => 4,
            'won' => false,
            'user_id' => 4
        ]);
        // User 5 rolls
        Roll::create([
            'dice1' => 4,
            'dice2' => 3,
            'won' => true,
            'user_id' => 5
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 4,
            'won' => false,
            'user_id' => 5
        ]);
        Roll::create([
            'dice1' => 5,
            'dice2' => 2,
            'won' => true,
            'user_id' => 5
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 1,
            'won' => true,
            'user_id' => 5
        ]);
        Roll::create([
            'dice1' => 4,
            'dice2' => 4,
            'won' => false,
            'user_id' => 5
        ]);
        // User 6 rolls
        Roll::create([
            'dice1' => 4,
            'dice2' => 3,
            'won' => true,
            'user_id' => 6
        ]);
        Roll::create([
            'dice1' => 1,
            'dice2' => 6,
            'won' => true,
            'user_id' => 6
        ]);
        Roll::create([
            'dice1' => 2,
            'dice2' => 5,
            'won' => true,
            'user_id' => 6
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 1,
            'won' => true,
            'user_id' => 6
        ]);
        Roll::create([
            'dice1' => 4,
            'dice2' => 3,
            'won' => true,
            'user_id' => 6
        ]);
        // User 7 rolls
        Roll::create([
            'dice1' => 1,
            'dice2' => 3,
            'won' => false,
            'user_id' => 7
        ]);
        Roll::create([
            'dice1' => 1,
            'dice2' => 4,
            'won' => false,
            'user_id' => 7
        ]);
        Roll::create([
            'dice1' => 2,
            'dice2' => 2,
            'won' => false,
            'user_id' => 7
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 3,
            'won' => false,
            'user_id' => 7
        ]);
        Roll::create([
            'dice1' => 4,
            'dice2' => 4,
            'won' => false,
            'user_id' => 7
        ]);
        // User 8 rolls
        Roll::create([
            'dice1' => 4,
            'dice2' => 3,
            'won' => true,
            'user_id' => 8
        ]);
        Roll::create([
            'dice1' => 1,
            'dice2' => 6,
            'won' => true,
            'user_id' => 8
        ]);
        Roll::create([
            'dice1' => 2,
            'dice2' => 5,
            'won' => true,
            'user_id' => 8
        ]);
        Roll::create([
            'dice1' => 6,
            'dice2' => 1,
            'won' => true,
            'user_id' => 8
        ]);
        Roll::create([
            'dice1' => 4,
            'dice2' => 3,
            'won' => true,
            'user_id' => 8
        ]);

    }
}

