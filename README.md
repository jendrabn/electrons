
# ELECTRONS — Project Documentation

Dokumentasi lengkap untuk proyek Laravel "electrons". Berisi ringkasan fitur, teknologi, struktur proyek, dan panduan deployment step-by-step pada Ubuntu 24 menggunakan PHP 8.3, Composer, MySQL terbaru, Redis, Supervisor, Nginx, dan Certbot SSL. Juga disertakan contoh konfigurasi `.env` untuk environment production.

## Ringkasan Singkat

`ELECTRONS` adalah aplikasi berbasis Laravel (versi 12) dengan UI dan admin panel menggunakan Filament v4 dan Livewire v3. Aplikasi menangani posting, thread, komentar, like, bookmark, tagging, audit log, dan role-based access control. Struktur proyek mengikuti konvensi Laravel modern.

Catatan: penjelasan untuk controller dan model dibuat singkat sesuai permintaan — tidak dijabarkan baris demi baris.

## Fitur Utama

- Manajemen User (roles & permissions)
- Posting, komentar, like, tag, category
- Thread & bookmark
- Audit log untuk aktivitas penting
- Panel Admin & Author (Filament)
- Queue & worker (Redis)
- Multi-locale (en / id)
- Notifikasi via email (SMTP)
- Testing dengan PHPUnit

## Teknologi & Dependensi Penting

- PHP 8.3 (direkomendasikan untuk deployment production)
- Laravel 12
- Filament v4
- Livewire v3
- Tailwind CSS v4
- MySQL (latest)
- Redis (cache & queue)
- Composer
- NPM / Vite (assets)
- Nginx + PHP-FPM
- Supervisor (queue worker)
- Certbot (Let's Encrypt)

## Struktur Proyek (penting)

- `app/Models/` — Model Eloquent (User, Post, Thread, Tag, Comment, Like, Bookmark, AuditLog, dll.)
- `app/Http/Controllers/` — Controller untuk web/API
- `app/Filament/` — Panel Filament (Admin, Author, Shared)
- `resources/views/` — Blade views (mis. `resources/views/posts/index.blade.php`)
- `routes/web.php` — Route web
- `database/migrations/` — Migrasi database
- `database/seeders/` — Seeder
- `config/` — Konfigurasi aplikasi

## Ringkasan Routes, Controller, Model

- Routes utama didefinisikan di `routes/web.php` (web) dan console di `routes/console.php`.
- Controller meng-handle request web & API untuk fitur posting, thread, komentar, autentikasi, dan halaman Filament.
- Model utama: `User`, `Post`, `Thread`, `Tag`, `Like`, `PostComment`, `ThreadComment`, `ThreadBookmark`, `AuditLog`, `Category`.

Catatan: Untuk detail implementasi (metode di controller atau relasi model) periksa file-file terkait di `app/Http/Controllers/` dan `app/Models/`.

## Prasyarat (untuk server production Ubuntu 24)

- Ubuntu 24 LTS
- PHP 8.3 + php8.3-fpm dan ekstensi: mbstring, curl, xml, zip, pdo_mysql, redis, gd/intl (sesuaikan kebutuhan)
- Composer
- MySQL (atau MariaDB) terbaru
- Redis
- Nginx
- Supervisor
- Certbot (python3-certbot-nginx)
- Node.js & npm (untuk build assets jika diperlukan)

## Langkah Deployment (step-by-step)

Semua perintah diasumsikan dijalankan sebagai user dengan sudo (kecuali yang eksplisit untuk akun `www-data`). Ganti `yourdomain.com` dan `/path/to/electrons` sesuai environment Anda.

### 1) Install packages dasar

```bash
sudo apt update
sudo apt install -y git curl unzip nginx supervisor redis-server mysql-server \
    php8.3 php8.3-fpm php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-intl php8.3-gd php8.3-mysql php8.3-redis \
    composer nodejs npm certbot python3-certbot-nginx
```

Jika `php8.3` tidak tersedia di repository default, tambahkan PPA yang sesuai atau gunakan repositori pihak ketiga yang tepercaya.

### 2) Clone project & install PHP deps

```bash
cd /var/www
sudo git clone https://github.com/jendrabn/electrons.git
sudo chown -R $USER:$USER electrons
cd electrons
composer install --no-dev --optimize-autoloader
```

Jika Composer belum terpasang secara global, gunakan installer resmi Composer.

### 3) Build frontend assets (WAJIB saat deployment)

Catatan penting: saat deployment wajib menjalankan `npm run build` sebelum langkah optimasi Laravel, restart service, atau mengaktifkan cache. Melewatkan build akan menyebabkan error "Unable to locate file in Vite manifest" dan aset frontend tidak tersedia.

```bash
npm install
npm run build
```

Jika Anda tidak ingin membangun assets pada server production secara langsung, bangun assets di pipeline CI/CD dan unggah hasil build ke `public/build` sebelum melakukan deploy; tetapi langkah build tetap wajib dilakukan di proses deploy (entah di server atau CI).

### 4) Konfigurasi `.env` untuk production

Salin file contoh dan edit: `cp .env.example .env` lalu sesuaikan. Contoh `.env` production lengkap ada di bawah.

Setelah `.env` disiapkan, generate app key:

```bash
php artisan key:generate
```

### 5) Database: buat database dan user

```bash
sudo mysql -u root
-- di dalam mysql prompt:
CREATE DATABASE electrons CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'electrons_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL ON electrons.* TO 'electrons_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 6) Migrasi & Seeder

```bash
php artisan migrate --force
php artisan db:seed --force
```

Jika project memiliki seed sensitive, cek isi seeder sebelum menjalankan di production.

### 7) Permissions (chown & chmod)

```bash
sudo chown -R www-data:www-data /var/www/electrons/storage /var/www/electrons/bootstrap/cache
sudo chmod -R 775 /var/www/electrons/storage /var/www/electrons/bootstrap/cache
```

Jika Anda menggunakan CI/CD, sesuaikan owner dan permission sesuai user proses PHP-FPM.

### 8) Cache & Optimisasi Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

Catatan: Jangan melakukan `route:cache` jika aplikasi menggunakan Closure-based routes.

### 9) Setup Supervisor untuk queue worker (Redis)

Contoh file: `/etc/supervisor/conf.d/electrons-worker.conf`

```ini
[program:electrons-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/electrons/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/electrons/storage/logs/worker.log
```

Kemudian:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start electrons-worker:*
```

### 10) Konfigurasi Nginx

Contoh konfigurasi Nginx di `/etc/nginx/sites-available/electrons`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/electrons/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Aktifkan konfigurasi dan reload Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/electrons /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 11) Pasang SSL dengan Certbot

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Certbot akan mengubah konfigurasi Nginx dan menambahkan redirect ke HTTPS.

### 12) Restart service terkait

```bash
sudo systemctl restart php8.3-fpm nginx redis supervisor
```

## Contoh `.env` Production

Ganti nilai-nilai placeholder dengan nilai nyata Anda.

```env
APP_NAME=Electrons
APP_ENV=production
APP_KEY=base64:GENERATED_BY_ARTISAN
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_LOCALE=id

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=electrons
DB_USERNAME=electrons_user
DB_PASSWORD=secure_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail_user@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail_user@gmail.com
MAIL_FROM_NAME="Electrons"

# OAuth / Socialite
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT=https://yourdomain.com/auth/google/callback

# Additional production flags
APP_TRUSTED_PROXIES=127.0.0.1
TRUSTED_PROXIES=* # (opsional; gunakan dengan hati-hati)

# Optional: disable debugbar
DEBUGBAR_ENABLED=false
-```

Tips:
- Untuk Gmail SMTP di production gunakan App Password (bukan password akun). Aktifkan 2FA dan buat App Password di Google Account.
- Pastikan `APP_URL` menggunakan `https://` jika Anda memakai SSL.

## Local Development (singkat)

```bash
cp .env.example .env
composer install
npm install
npm run dev
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

## Testing

- Jalankan test suite yang relevan:

```bash
php artisan test
```

Atau jalankan file test tertentu:

```bash
php artisan test tests/Feature/SomeFeatureTest.php
```

## Catatan Keamanan & Operasional

- Jangan commit `.env` ke repositori.
- Pakai App Password untuk SMTP Gmail.
- Batasi akses SSH dan gunakan kunci publik/privat.
- Pastikan backup database dan rotasi logs.
- Pertimbangkan rate limiting, monitoring, dan alerting.

## Troubleshooting Singkat

- Jika Vite error: "Unable to locate file in Vite manifest" — jalankan `npm run build` atau `composer run dev` seperti instruksi proyek.
- Jika queue worker tidak jalan: cek Supervisor logs di `/var/www/electrons/storage/logs/worker.log`.
- Jika permission error: ulangi langkah chown/chmod untuk `storage` dan `bootstrap/cache`.

## Penutup

README ini mencakup ringkasan proyek, dependensi, dan langkah lengkap untuk deploy di Ubuntu 24 dengan PHP 8.3, MySQL, Redis, Nginx, Supervisor, dan Certbot. Jika Anda ingin saya menambahkan contoh konfigurasi Nginx untuk HTTP/2 atau contoh pipeline CI/CD (GitHub Actions), beri tahu saya dan saya akan tambah.

## Contoh CI/CD (GitHub Actions)

Contoh workflow yang membangun assets, menjalankan test, dan menyalin hasil build ke server via rsync/SSH. Sesuaikan secrets di repository (`SSH_HOST`, `SSH_USER`, `SSH_KEY`, `DEPLOY_PATH`).

```yaml
name: CI/CD

on:
    push:
        branches: [ main ]

jobs:
    build-and-deploy:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v4

            - name: Setup Node
                uses: actions/setup-node@v4
                with:
                    node-version: '20'

            - name: Setup PHP
                uses: shivammathur/setup-php@v3
                with:
                    php-version: '8.3'
                    extensions: mbstring, curl, xml, zip, intl, gd, redis

            - name: Install Composer deps
                run: composer install --no-dev --optimize-autoloader

            - name: Install NPM deps & build
                run: |
                    npm ci
                    npm run build

            - name: Run tests
                run: composer test || php artisan test

            - name: Deploy (rsync)
                env:
                    SSH_PRIVATE_KEY: ${{ secrets.SSH_KEY }}
                run: |
                    mkdir -p ~/.ssh
                    echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
                    chmod 600 ~/.ssh/id_rsa
                    rsync -avz --delete --exclude='.env' --exclude='vendor' --exclude='node_modules' ./ ${{ secrets.SSH_USER }}@${{ secrets.SSH_HOST }}:${{ secrets.DEPLOY_PATH }}
```

Catatan: workflow ini melakukan rsync semua file; lebih baik gunakan build artefact dan deploy hanya artefak produksi atau gunakan runner deploy yang lebih aman.

## Contoh systemd unit untuk queue worker

Jika Anda lebih suka systemd daripada Supervisor, contoh unit file `/etc/systemd/system/electrons-worker.service`:

```ini
[Unit]
Description=Laravel Queue Worker (electrons)
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/electrons/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=electrons-worker

[Install]
WantedBy=multi-user.target
```

Setelah membuat file:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now electrons-worker
sudo journalctl -u electrons-worker -f
```

## Contoh Nginx (HTTP/2) + Security Headers

Contoh konfigurasi Nginx dengan HTTP/2, HSTS, dan header keamanan. Letakkan di `/etc/nginx/sites-available/electrons-https` dan sesuaikan `ssl_certificate` path jika Certbot yang mengelola SSL.

```nginx
server {
        listen 443 ssl http2;
        server_name yourdomain.com www.yourdomain.com;
        root /var/www/electrons/public;

        ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
        ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
        include /etc/letsencrypt/options-ssl-nginx.conf;
        ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

        add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header Referrer-Policy "no-referrer-when-downgrade" always;
        add_header X-XSS-Protection "1; mode=block" always;

        index index.php;

        location / {
                try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }

        location ~ /\.ht {
                deny all;
        }
}

server {
        listen 80;
        server_name yourdomain.com www.yourdomain.com;
        return 301 https://$host$request_uri;
}
```

Catatan: selalu tes konfigurasi Nginx `sudo nginx -t` sebelum reload.

---

Jika Anda ingin, saya bisa membuat file GitHub Actions workflow (`.github/workflows/deploy.yml`) langsung di repo atau membuat contoh `systemd`/`supervisor` configuration files. Sebutkan mana yang Anda inginkan dan saya akan tambahkan.

