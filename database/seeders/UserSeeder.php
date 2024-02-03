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
            'name' => 'mortadelo',
            'email' => 'mortadelo@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '3',
            'name' => 'filemon',
            'email' => 'filemon@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '4',
            'name' => 'mafalda',
            'email' => 'mafalda@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

        User::create([
            'id' => '5',
            'name' => 'snoopy',
            'email' => 'snoopy@email.com',
            'password' => bcrypt('12345678')
        ])->assignRole('player');

    }
}

