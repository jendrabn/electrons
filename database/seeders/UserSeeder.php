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
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Author User',
            'email' => 'author@mail.com',
            'password' => bcrypt('password'),
        ])->assignRole('author');
    }
}
