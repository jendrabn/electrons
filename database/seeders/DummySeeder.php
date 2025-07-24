<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = json_decode(file_get_contents(database_path('data/categories.json')), true);
        $tags = json_decode(file_get_contents(database_path('data/tags.json')), true);

        Category::insert($categories);
        Tag::insert($tags);

        $this->call(RoleAndPermissionSeeder::class);

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

        User::factory(25)->create();
        Post::factory(50)->create();
    }
}
