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
        $userAdmin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
        ]);
        $userAdmin->assignRole('admin');

        $userAuthor = User::factory()->create([
            'name' => 'Author',
            'email' => 'author@mail.com',
        ]);
        $userAuthor->assignRole('author');
    }
}
