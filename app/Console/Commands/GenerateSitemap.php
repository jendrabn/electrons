<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Thread;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL as FacadesUrl;
use Illuminate\Support\Str;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate
                            {--base= : Override base URL}
                            {--path=sitemaps : Output dir relative to public_path}
                            {--chunk=1000 : Chunk size}
                            {--gzip : Also write .gz copies}';

    protected $description = 'Generate sitemap.xml (index + per-seksi) menggunakan spatie/laravel-sitemap';

    public function handle(): int
    {
        $tStart = microtime(true);

        $baseUrl = rtrim($this->option('base') ?: (config('app.url') ?: url('/')), '/');
        $outDir  = public_path(trim($this->option('path'), '/'));
        $chunk   = max(100, (int) $this->option('chunk'));
        $withGz  = (bool) $this->option('gzip');

        if (!is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }

        // ====== BERSIHKAN FILE LAMA DULU (otomatis tiap generate) ======
        $this->cleanOutputDir($outDir);

        $indexPath      = $outDir . '/sitemap.xml';
        $homePath       = $outDir . '/sitemap-home.xml';
        $postsPath      = $outDir . '/sitemap-posts.xml';
        $threadsPath    = $outDir . '/sitemap-threads.xml';
        $categoriesPath = $outDir . '/sitemap-categories.xml';
        $tagsPath       = $outDir . '/sitemap-tags.xml';
        $authorsPath    = $outDir . '/sitemap-authors.xml';
        $staticPath     = $outDir . '/sitemap-static.xml';

        $this->generateHomeSitemap($homePath);
        $this->generatePostsSitemap($postsPath, $chunk);
        $this->generateThreadsSitemap($threadsPath, $chunk);
        $this->generateCategoriesSitemap($categoriesPath);
        $this->generateTagsSitemap($tagsPath);
        $this->generateAuthorsSitemap($authorsPath);
        $this->generateStaticSitemap($staticPath);

        // Normalisasi prefix publik ('' atau '/sitemaps')
        $publicPrefix = Str::after($outDir, public_path());
        $publicPrefix = $publicPrefix ? '/' . trim($publicPrefix, '/') : '';

        $index = SitemapIndex::create()
            ->add($baseUrl . $publicPrefix . '/sitemap-home.xml')
            ->add($baseUrl . $publicPrefix . '/sitemap-posts.xml')
            ->add($baseUrl . $publicPrefix . '/sitemap-threads.xml')
            ->add($baseUrl . $publicPrefix . '/sitemap-categories.xml')
            ->add($baseUrl . $publicPrefix . '/sitemap-tags.xml')
            ->add($baseUrl . $publicPrefix . '/sitemap-authors.xml')
            ->add($baseUrl . $publicPrefix . '/sitemap-static.xml');

        $index->writeToFile($indexPath);

        // Backwards compatibility: copy index to public root as sitemap.xml
        try {
            @copy($indexPath, public_path('sitemap.xml'));
        } catch (\Throwable $e) {
            // Failing to copy is non-fatal; just warn the user
            $this->warn('Gagal menyalin sitemap index ke public/sitemap.xml: ' . $e->getMessage());
        }

        if ($withGz) {
            foreach ([$homePath, $postsPath, $threadsPath, $categoriesPath, $tagsPath, $authorsPath, $staticPath, $indexPath] as $p) {
                $this->gzipCopy($p);
            }
        }

        $elapsed = number_format(microtime(true) - $tStart, 2);
        $this->info("Sitemap generated to: {$publicPrefix}/ (done in {$elapsed}s)");
        $this->line('Robots hint: Sitemap: ' . $baseUrl . $publicPrefix . '/sitemap.xml');

        return self::SUCCESS;
    }

    /**
     * Hapus semua file sitemap lama di output dir (sitemap*.xml dan .xml.gz).
     */
    private function cleanOutputDir(string $outDir): void
    {
        foreach (glob($outDir . '/sitemap*.xml') ?: [] as $f) {
            @unlink($f);
        }
        foreach (glob($outDir . '/sitemap*.xml.gz') ?: [] as $f) {
            @unlink($f);
        }
    }

    private function generateHomeSitemap(string $path): void
    {
        $sm = Sitemap::create();

        // Home (pakai nama rute)
        $sm->add(
            Url::create(FacadesUrl::route('home', [], true))
                ->setLastModificationDate(Carbon::now()->startOfDay())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Listing posts
        $sm->add(
            Url::create(FacadesUrl::route('posts.index', [], true))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        );

        // Community / threads listing
        $sm->add(
            Url::create(FacadesUrl::route('comunity.index', [], true))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8)
        );

        $sm->writeToFile($path);
    }

    private function generatePostsSitemap(string $path, int $chunk): void
    {
        $sm = Sitemap::create();

        Post::query()
            ->select(['id', 'slug', 'updated_at', 'published_at'])
            // Perbaikan: cek scopePublished (bukan 'published' langsung)
            ->when(method_exists(Post::class, 'scopePublished'), fn($q) => $q->published())
            ->orderBy('id')
            ->chunkById($chunk, function ($posts) use ($sm) {
                foreach ($posts as $post) {
                    $url = FacadesUrl::route('posts.show', $post, true);
                    $lastmod = $post->updated_at ?? $post->published_at ?? now();

                    $sm->add(
                        Url::create($url)
                            ->setLastModificationDate($lastmod instanceof \DateTimeInterface ? $lastmod : Carbon::parse($lastmod))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
                }
            });

        $sm->writeToFile($path);
    }

    private function generateThreadsSitemap(string $path, int $chunk): void
    {
        $sm = Sitemap::create();

        Thread::query()
            ->select(['id', 'slug', 'updated_at'])
            ->where('is_hidden', false)
            ->orderBy('id')
            ->chunkById($chunk, function ($threads) use ($sm) {
                foreach ($threads as $thread) {
                    $url = FacadesUrl::route('comunity.show', $thread, true);
                    $lastmod = $thread->updated_at ?? now();

                    $sm->add(
                        Url::create($url)
                            ->setLastModificationDate($lastmod instanceof \DateTimeInterface ? $lastmod : Carbon::parse($lastmod))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                }
            });

        $sm->writeToFile($path);
    }

    private function generateCategoriesSitemap(string $path): void
    {
        $sm = Sitemap::create();

        Category::query()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(1000, function ($cats) use ($sm) {
                foreach ($cats as $cat) {
                    $sm->add(
                        Url::create(FacadesUrl::route('posts.category', $cat, true))
                            ->setLastModificationDate($cat->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                }
            });

        $sm->writeToFile($path);
    }

    private function generateTagsSitemap(string $path): void
    {
        $sm = Sitemap::create();

        Tag::query()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(1000, function ($tags) use ($sm) {
                foreach ($tags as $tag) {
                    $sm->add(
                        Url::create(FacadesUrl::route('posts.tag', $tag, true))
                            ->setLastModificationDate($tag->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.5)
                    );
                }
            });

        $sm->writeToFile($path);
    }

    private function generateAuthorsSitemap(string $path): void
    {
        $sm = Sitemap::create();

        User::query()
            ->select(['id', 'name', 'updated_at'])
            ->orderBy('id')
            ->chunkById(1000, function ($users) use ($sm) {
                foreach ($users as $user) {
                    $sm->add(
                        Url::create(FacadesUrl::route('posts.author', $user, true))
                            ->setLastModificationDate($user->updated_at ?? now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.4)
                    );
                }
            });

        $sm->writeToFile($path);
    }

    private function generateStaticSitemap(string $path): void
    {
        $sm = Sitemap::create();

        // Halaman statis — semua via nama rute
        $static = [
            ['route' => 'home',    'priority' => 1.0, 'freq' => Url::CHANGE_FREQUENCY_DAILY],
            ['route' => 'about',   'priority' => 0.3, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'contact', 'priority' => 0.3, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($static as $item) {
            $sm->add(
                Url::create(FacadesUrl::route($item['route'], [], true))
                    ->setLastModificationDate(Carbon::now()->startOfDay())
                    ->setChangeFrequency($item['freq'])
                    ->setPriority($item['priority'])
            );
        }

        $sm->writeToFile($path);
    }

    private function gzipCopy(string $xmlPath): void
    {
        if (!function_exists('gzopen')) {
            $this->warn('Lewati gzip untuk ' . basename($xmlPath) . ' (zlib tidak tersedia).');
            return;
        }
        if (!is_file($xmlPath)) return;

        $gzPath = $xmlPath . '.gz';
        $in     = fopen($xmlPath, 'rb');
        $out    = gzopen($gzPath, 'wb9');
        while (!feof($in)) {
            gzwrite($out, fread($in, 1024 * 512));
        }
        fclose($in);
        gzclose($out);
    }
}
