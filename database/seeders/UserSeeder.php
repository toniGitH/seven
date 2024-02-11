<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '1',
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('admin');

        User::create([
            'id' => '2',
            'name' => 'admin2',
            'email' => 'admin2@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('admin');

        User::create([
            'id' => '3',
            'name' => 'mortadelo',
            'email' => 'mortadelo@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '4',
            'name' => 'filemon',
            'email' => 'filemon@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '5',
            'name' => 'mafalda',
            'email' => 'mafalda@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '6',
            'name' => 'snoopy',
            'email' => 'snoopy@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '7',
            'name' => 'admin6',
            'email' => 'admin6@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('admin');

        User::create([
            'id' => '8',
            'name' => 'espinete',
            'email' => 'espinete@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '9',
            'name' => 'caponata',
            'email' => 'caponata@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');



    }
}

