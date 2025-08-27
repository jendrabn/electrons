<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapService
{
    /**
     * Generate sitemap and save to public directory
     */
    public function generate(): array
    {
        $sitemap = Sitemap::create();
        $stats = [
            'homepage' => 0,
            'blog_pages' => 0,
            'posts' => 0,
            'categories' => 0,
            'tags' => 0,
            'authors' => 0,
            'total' => 0
        ];

        // Add homepage
        $sitemap->add(
            Url::create(route('home'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );
        $stats['homepage'] = 1;

        // Add blog index page
        $sitemap->add(
            Url::create(route('posts.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        );
        $stats['blog_pages'] = 1;

        // Add all published posts
        Post::query()
            ->published()
            ->orderBy('updated_at', 'desc')
            ->chunk(100, function ($posts) use ($sitemap, &$stats) {
                foreach ($posts as $post) {
                    $sitemap->add(
                        Url::create(route('posts.show', $post))
                            ->setLastModificationDate($post->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
                    $stats['posts']++;
                }
            });

        // Add all categories with published posts
        Category::query()
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->chunk(50, function ($categories) use ($sitemap, &$stats) {
                foreach ($categories as $category) {
                    $sitemap->add(
                        Url::create(route('posts.category', $category))
                            ->setLastModificationDate($category->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                    $stats['categories']++;
                }
            });

        // Add all tags with published posts
        Tag::query()
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->chunk(50, function ($tags) use ($sitemap, &$stats) {
                foreach ($tags as $tag) {
                    $sitemap->add(
                        Url::create(route('posts.tag', $tag))
                            ->setLastModificationDate($tag->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                    $stats['tags']++;
                }
            });

        // Add all authors with published posts
        User::query()
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->chunk(50, function ($users) use ($sitemap, &$stats) {
                foreach ($users as $user) {
                    $sitemap->add(
                        Url::create(route('posts.author', $user))
                            ->setLastModificationDate($user->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.5)
                    );
                    $stats['authors']++;
                }
            });

        // Calculate total
        $stats['total'] = $stats['homepage'] + $stats['blog_pages'] + $stats['posts'] +
            $stats['categories'] + $stats['tags'] + $stats['authors'];

        // Save sitemap to public directory
        $sitemap->writeToFile(public_path('sitemap.xml'));

        return $stats;
    }

    /**
     * Check if sitemap exists and is recent
     */
    public function isRecentlyGenerated(int $maxAgeHours = 24): bool
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return false;
        }

        $lastModified = filemtime($sitemapPath);
        $maxAge = $maxAgeHours * 3600; // Convert hours to seconds

        return (time() - $lastModified) < $maxAge;
    }

    /**
     * Get sitemap file information
     */
    public function getFileInfo(): ?array
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!file_exists($sitemapPath)) {
            return null;
        }

        return [
            'path' => $sitemapPath,
            'size' => filesize($sitemapPath),
            'last_modified' => filemtime($sitemapPath),
            'last_modified_human' => date('Y-m-d H:i:s', filemtime($sitemapPath)),
        ];
    }
}
