<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sitemap:generate --gzip')
    ->everySixHours()
    ->onFailure(function () {
        Log::error('Sitemap generation gagal pada ' . now()->toDateTimeString());
    })
    ->onSuccess(function () {
        Log::info('Sitemap berhasil dibuat pada ' . now()->toDateTimeString());
    });;
