<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'remember_token' => str()->random(10),
        ]);
        $admin->assignRole('admin');

        // author user
        $author = User::create([
            'name' => 'Author',
            'email' => 'author@mail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'remember_token' => str()->random(10),
        ]);
        $author->assignRole('author');
    }
}
