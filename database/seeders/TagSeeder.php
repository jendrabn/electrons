<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // 🌐 WEB & DASAR
            ['name' => 'HTML', 'slug' => 'html', 'description' => 'Dasar dan lanjutan HTML untuk membangun struktur halaman web.'],
            ['name' => 'CSS', 'slug' => 'css', 'description' => 'Tutorial CSS, teknik styling, layout modern, animasi, dan framework CSS populer.'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'Bahasan seputar JavaScript, ES6+, dan teknik modern untuk web development.'],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'description' => 'Tutorial TypeScript untuk aplikasi modern berbasis JavaScript.'],
            ['name' => 'PHP', 'slug' => 'php', 'description' => 'Bahasan lengkap seputar PHP, framework, serta tips membangun aplikasi berbasis PHP.'],
            ['name' => 'Python', 'slug' => 'python', 'description' => 'Bahasan Python untuk web, data science, AI, dan scripting.'],
            ['name' => 'Java', 'slug' => 'java', 'description' => 'Tutorial Java dan framework populer seperti Spring Boot.'],
            ['name' => 'C', 'slug' => 'c', 'description' => 'Bahasan seputar bahasa pemrograman C dan dasar sistem.'],
            ['name' => 'C++', 'slug' => 'c-plus-plus', 'description' => 'Artikel tentang C++ untuk aplikasi desktop, embedded, dan game development.'],
            ['name' => 'C#', 'slug' => 'c-sharp', 'description' => 'Tutorial C# untuk aplikasi desktop, Unity, dan .NET.'],
            ['name' => 'Go', 'slug' => 'go', 'description' => 'Bahasa pemrograman Go (Golang) untuk backend, cloud, dan sistem terdistribusi.'],
            ['name' => 'Rust', 'slug' => 'rust', 'description' => 'Bahasa pemrograman Rust untuk sistem yang aman dan cepat.'],
            ['name' => 'Swift', 'slug' => 'swift', 'description' => 'Tutorial bahasa Swift untuk iOS dan macOS.'],
            ['name' => 'Kotlin', 'slug' => 'kotlin', 'description' => 'Tutorial Kotlin untuk Android dan aplikasi multiplatform.'],
            ['name' => 'Ruby', 'slug' => 'ruby', 'description' => 'Artikel tentang Ruby dan framework Ruby on Rails.'],
            ['name' => 'Dart', 'slug' => 'dart', 'description' => 'Bahasa Dart untuk Flutter dan aplikasi mobile modern.'],

            // 📱 MOBILE & FRONTEND
            ['name' => 'Android', 'slug' => 'android', 'description' => 'Pengembangan aplikasi Android menggunakan Java, Kotlin, atau framework modern.'],
            ['name' => 'iOS', 'slug' => 'ios', 'description' => 'Pengembangan aplikasi iOS menggunakan Swift atau Objective-C.'],
            ['name' => 'Flutter', 'slug' => 'flutter', 'description' => 'Framework Flutter untuk membuat aplikasi mobile multiplatform.'],
            ['name' => 'React Native', 'slug' => 'react-native', 'description' => 'Pengembangan aplikasi mobile cross-platform dengan React Native.'],
            ['name' => 'NativeScript', 'slug' => 'nativescript', 'description' => 'Framework untuk membangun aplikasi mobile native dengan JavaScript/TypeScript.'],

            // ⚡ FRAMEWORK JS
            ['name' => 'Vue', 'slug' => 'vue', 'description' => 'Artikel tentang Vue.js untuk front-end development.'],
            ['name' => 'Nuxt.js', 'slug' => 'nuxtjs', 'description' => 'Framework Nuxt.js untuk aplikasi Vue dengan SSR dan SSG.'],
            ['name' => 'Vuex', 'slug' => 'vuex', 'description' => 'State management dengan Vuex untuk aplikasi Vue.js.'],
            ['name' => 'ReactJS', 'slug' => 'reactjs', 'description' => 'Materi tentang ReactJS, library UI populer untuk aplikasi web modern.'],
            ['name' => 'Next.js', 'slug' => 'nextjs', 'description' => 'Framework React Next.js untuk SSR dan static site generation.'],
            ['name' => 'Angular', 'slug' => 'angular', 'description' => 'Framework Angular untuk membangun aplikasi web modern.'],
            ['name' => 'Redux', 'slug' => 'redux', 'description' => 'Tutorial Redux untuk state management di aplikasi React.'],
            ['name' => 'Alpine.js', 'slug' => 'alpinejs', 'description' => 'Framework ringan Alpine.js untuk interaktivitas frontend.'],
            ['name' => 'Inertia.js', 'slug' => 'inertiajs', 'description' => 'Framework Inertia.js untuk menghubungkan Laravel dengan Vue/React.'],

            // 🖥️ LARAVEL & EKOSISTEM
            ['name' => 'Laravel', 'slug' => 'laravel', 'description' => 'Framework PHP populer untuk aplikasi web modern.'],
            ['name' => 'Filament', 'slug' => 'filament', 'description' => 'Admin panel Laravel modern berbasis Filament PHP.'],
            ['name' => 'Livewire', 'slug' => 'livewire', 'description' => 'Laravel Livewire untuk membuat UI interaktif tanpa JavaScript kompleks.'],
            ['name' => 'Laravel Livewire', 'slug' => 'laravel-livewire', 'description' => 'Artikel tentang penggunaan Livewire di Laravel.'],
            ['name' => 'Eloquent', 'slug' => 'eloquent', 'description' => 'ORM Laravel untuk bekerja dengan database.'],
            ['name' => 'Sanctum', 'slug' => 'sanctum', 'description' => 'Laravel Sanctum untuk autentikasi API dan SPA.'],
            ['name' => 'Passport', 'slug' => 'passport', 'description' => 'Laravel Passport untuk autentikasi berbasis OAuth2.'],
            ['name' => 'Horizon', 'slug' => 'horizon', 'description' => 'Queue monitoring dengan Laravel Horizon.'],
            ['name' => 'Telescope', 'slug' => 'telescope', 'description' => 'Debugging dan monitoring Laravel dengan Telescope.'],
            ['name' => 'Jetstream', 'slug' => 'jetstream', 'description' => 'Starter kit Laravel Jetstream untuk autentikasi dan manajemen tim.'],
            ['name' => 'Breeze', 'slug' => 'breeze', 'description' => 'Starter kit Laravel Breeze untuk autentikasi ringan.'],
            ['name' => 'Sail', 'slug' => 'sail', 'description' => 'Laravel Sail untuk development environment berbasis Docker.'],
            ['name' => 'Mix', 'slug' => 'mix', 'description' => 'Laravel Mix untuk mengelola asset menggunakan Webpack.'],
            ['name' => 'Vite', 'slug' => 'vite', 'description' => 'Laravel Vite untuk mengelola asset modern.'],
            ['name' => 'Nova', 'slug' => 'nova', 'description' => 'Admin panel premium Laravel Nova.'],
            ['name' => 'Forge', 'slug' => 'forge', 'description' => 'Layanan Laravel Forge untuk manajemen server & deployment otomatis.'],
            ['name' => 'Envoyer', 'slug' => 'envoyer', 'description' => 'Zero-downtime deployment menggunakan Laravel Envoyer.'],
            ['name' => 'Valet', 'slug' => 'valet', 'description' => 'Laravel Valet untuk pengembangan lokal di macOS.'],
            ['name' => 'Octane', 'slug' => 'octane', 'description' => 'Laravel Octane untuk performa tinggi dengan Swoole/RoadRunner.'],
            ['name' => 'Pint', 'slug' => 'pint', 'description' => 'Laravel Pint untuk code style fixer otomatis.'],
            ['name' => 'Spatie', 'slug' => 'spatie', 'description' => 'Package Laravel dari Spatie yang powerful.'],
            ['name' => 'Seeder', 'slug' => 'seeder', 'description' => 'Laravel Seeder untuk mengisi data awal ke database.'],
            ['name' => 'Migration', 'slug' => 'migration', 'description' => 'Laravel Migration untuk manajemen struktur database.'],
            ['name' => 'Queue', 'slug' => 'queue', 'description' => 'Laravel Queue untuk eksekusi job secara asynchronous.'],
            ['name' => 'Scheduler', 'slug' => 'scheduler', 'description' => 'Laravel Scheduler untuk cron job otomatis.'],
            ['name' => 'Blade', 'slug' => 'blade', 'description' => 'Blade templating engine Laravel.'],
            ['name' => 'Lumen', 'slug' => 'lumen', 'description' => 'Micro-framework Laravel untuk API ringan.'],

            // 🛢️ DATABASE & DATA
            ['name' => 'MySQL', 'slug' => 'mysql', 'description' => 'Database relasional populer untuk aplikasi web.'],
            ['name' => 'SQLite', 'slug' => 'sqlite', 'description' => 'Database ringan yang sering dipakai untuk aplikasi kecil.'],
            ['name' => 'MongoDB', 'slug' => 'mongodb', 'description' => 'Database NoSQL dokumen populer.'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'description' => 'Database relasional open-source dengan fitur canggih.'],
            ['name' => 'Database', 'slug' => 'database', 'description' => 'Bahasan umum tentang manajemen dan desain database.'],

            // 🤖 AI & DATA SCIENCE
            ['name' => 'Data Science', 'slug' => 'data-science', 'description' => 'Analisis data, machine learning, dan AI.'],
            ['name' => 'Machine Learning', 'slug' => 'machine-learning', 'description' => 'Pembelajaran mesin, algoritma, dan penerapannya.'],
            ['name' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence', 'description' => 'Kecerdasan buatan, deep learning, dan aplikasi praktis.'],
            ['name' => 'Big Data', 'slug' => 'big-data', 'description' => 'Teknologi big data, Hadoop, Spark, dan analitik.'],
            ['name' => 'Analisis', 'slug' => 'analisis', 'description' => 'Artikel analisis teknologi, tren, dan data.'],

            // ☁️ SERVER, VPS, CLOUD, DEVOPS
            ['name' => 'Server', 'slug' => 'server', 'description' => 'Manajemen server, konfigurasi, dan optimasi performa.'],
            ['name' => 'Linux', 'slug' => 'linux', 'description' => 'Tips dan tutorial sistem operasi Linux.'],
            ['name' => 'Windows', 'slug' => 'windows', 'description' => 'Penggunaan Windows untuk developer dan server.'],
            ['name' => 'Mac', 'slug' => 'mac', 'description' => 'Pengembangan software di macOS.'],
            ['name' => 'Hosting', 'slug' => 'hosting', 'description' => 'Panduan memilih hosting, optimasi, dan tips server.'],
            ['name' => 'VPS', 'slug' => 'vps', 'description' => 'Virtual Private Server, konfigurasi, dan deployment aplikasi.'],
            ['name' => 'Cloud Computing', 'slug' => 'cloud-computing', 'description' => 'Cloud service AWS, GCP, Azure, dan teknologi cloud lainnya.'],
            ['name' => 'AWS', 'slug' => 'aws', 'description' => 'Amazon Web Services untuk cloud computing.'],
            ['name' => 'Docker', 'slug' => 'docker', 'description' => 'Docker untuk containerization aplikasi.'],
            ['name' => 'Kubernetes', 'slug' => 'kubernetes', 'description' => 'Orkestrasi container menggunakan Kubernetes.'],
            ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'Praktik DevOps, CI/CD, dan automation.'],
            ['name' => 'CI/CD', 'slug' => 'ci-cd', 'description' => 'Continuous Integration dan Continuous Delivery.'],
            ['name' => 'Microservices', 'slug' => 'microservices', 'description' => 'Arsitektur microservices dan praktik terbaik.'],
            ['name' => 'Serverless', 'slug' => 'serverless', 'description' => 'Arsitektur serverless dan fungsinya di cloud.'],
            ['name' => 'Networking', 'slug' => 'networking', 'description' => 'Dasar dan lanjutan jaringan komputer.'],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity', 'description' => 'Keamanan siber, ancaman, dan pencegahan.'],
            ['name' => 'Security', 'slug' => 'security', 'description' => 'Tips keamanan aplikasi web, mobile, dan server.'],
            ['name' => 'IoT', 'slug' => 'iot', 'description' => 'Internet of Things, perangkat pintar, dan aplikasinya.'],

            // 🛠️ TOOLS & LAINNYA
            ['name' => 'Git', 'slug' => 'git', 'description' => 'Version control dengan Git dan GitHub/GitLab.'],
            ['name' => 'Text Editor', 'slug' => 'text-editor', 'description' => 'Editor populer seperti VS Code, Vim, dan Sublime.'],
            ['name' => 'Snippet', 'slug' => 'snippet', 'description' => 'Kumpulan snippet kode siap pakai.'],
            ['name' => 'Tool', 'slug' => 'tool', 'description' => 'Alat bantu developer dan desainer.'],

            // 📝 UMUM & MOTIVASI
            ['name' => 'Belajar', 'slug' => 'belajar', 'description' => 'Tutorial dasar dan lanjutan untuk belajar coding.'],
            ['name' => 'Tips dan Trik', 'slug' => 'tips-dan-trik', 'description' => 'Tips dan trik praktis untuk programmer.'],
            ['name' => 'Sharing', 'slug' => 'sharing', 'description' => 'Berbagi pengalaman, opini, dan cerita dunia IT.'],
            ['name' => 'Komunitas', 'slug' => 'komunitas', 'description' => 'Cerita dan kegiatan komunitas developer.'],
            ['name' => 'Startup', 'slug' => 'startup', 'description' => 'Dunia startup teknologi dan bisnis digital.'],
            ['name' => 'Karir', 'slug' => 'karir', 'description' => 'Tips membangun karir di dunia teknologi.'],
            ['name' => 'Produktivitas', 'slug' => 'produktivitas', 'description' => 'Tips meningkatkan produktivitas kerja dan coding.'],
            ['name' => 'Motivasi', 'slug' => 'motivasi', 'description' => 'Motivasi untuk developer agar tetap semangat.'],
            ['name' => 'Inspirasi', 'slug' => 'inspirasi', 'description' => 'Cerita inspiratif di dunia teknologi.'],
            ['name' => 'Soft Skill', 'slug' => 'softskill', 'description' => 'Soft skill yang penting untuk developer.'],
        ];

        foreach ($tags as $category) {
            Tag::create($category);
        }
    }
}
