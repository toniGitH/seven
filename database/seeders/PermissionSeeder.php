<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ROLES
        // -----------------------------------------------------------------------------------

        $admin=Role::create(['name' =>'admin']);
        $player=Role::create(['name'=>'player']);


        // PERMISSIONS
        // -----------------------------------------------------------------------------------

        Permission::create(['name' => 'players.index'])->syncRoles([$admin]);
        Permission::create(['name' => 'players.ranking'])->syncRoles([$admin]);
        Permission::create(['name' => 'players.winner'])->syncRoles([$admin]);
        Permission::create(['name' => 'players.loser'])->syncRoles([$admin]);

        Permission::create(['name' => 'players.store'])->syncRoles([$player]);
        Permission::create(['name' => 'players.destroy'])->syncRoles([$player]);
        Permission::create(['name' => 'players.show'])->syncRoles([$player]);
        Permission::create(['name' => 'players.getWinRate'])->syncRoles([$player]);
    }
}
