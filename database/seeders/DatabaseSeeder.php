<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        User::factory()
            ->create([
                'name' => 'Admin',
                'email' => 'admin@mail.com',
            ])
            ->assignRole('admin');
        User::factory()
            ->create([
                'name' => 'Author',
                'email' => 'author@mail.com',
            ])
            ->assignRole('author');
    }
}
