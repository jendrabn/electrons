<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pemrograman',
                'slug' => 'pemrograman',
                'description' => 'Artikel tentang berbagai bahasa pemrograman, framework, dan teknik coding.'
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Bahasan seputar pengembangan aplikasi web, frontend, backend, dan fullstack.'
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'Pengembangan aplikasi untuk Android, iOS, dan multiplatform.'
            ],
            [
                'name' => 'Database',
                'slug' => 'database',
                'description' => 'Artikel mengenai desain database, SQL, NoSQL, optimasi query, dan manajemen data.'
            ],
            [
                'name' => 'Cloud & Server',
                'slug' => 'cloud-server',
                'description' => 'Topik seputar VPS, server, DevOps, cloud computing, dan deployment aplikasi.'
            ],
            [
                'name' => 'Data Science & AI',
                'slug' => 'data-science-ai',
                'description' => 'Pembahasan data science, machine learning, kecerdasan buatan, dan big data.'
            ],
            [
                'name' => 'Laravel & PHP',
                'slug' => 'laravel-php',
                'description' => 'Artikel khusus tentang Laravel, ekosistemnya, dan bahasa pemrograman PHP.'
            ],
            [
                'name' => 'Framework & Tools',
                'slug' => 'framework-tools',
                'description' => 'Panduan dan tutorial framework populer serta tools untuk developer.'
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => 'Tips dan praktik terbaik untuk keamanan aplikasi web, mobile, dan server.'
            ],
            [
                'name' => 'Karir & Produktivitas',
                'slug' => 'karir-produktivitas',
                'description' => 'Artikel seputar karir di bidang IT, soft skill, komunitas, dan produktivitas.'
            ],
            [
                'name' => 'Inspirasi & Sharing',
                'slug' => 'inspirasi-sharing',
                'description' => 'Cerita inspiratif, opini, dan pengalaman seputar dunia teknologi.'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'description' => $category['description']]
            );
        }
    }
}
