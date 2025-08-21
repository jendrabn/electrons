<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for the blog';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();
        $totalUrls = 0;

        // Add home page
        $this->addHomepage($sitemap);
        $totalUrls += 1;

        // Add blog pages
        $this->addBlogPages($sitemap);
        $totalUrls += 1;

        // Add posts
        $postsCount = $this->addPosts($sitemap);
        $totalUrls += $postsCount;

        // Add categories
        $categoriesCount = $this->addCategories($sitemap);
        $totalUrls += $categoriesCount;

        // Add tags
        $tagsCount = $this->addTags($sitemap);
        $totalUrls += $tagsCount;

        // Add authors
        $authorsCount = $this->addAuthors($sitemap);
        $totalUrls += $authorsCount;

        // Save sitemap to public directory
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at public/sitemap.xml');
        $this->info('Total URLs: ' . $totalUrls);

        return Command::SUCCESS;
    }

    /**
     * Add homepage to sitemap
     */
    private function addHomepage(Sitemap $sitemap): void
    {
        $this->info('Adding homepage...');

        $sitemap->add(
            Url::create(route('home'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );
    }

    /**
     * Add blog index page to sitemap
     */
    private function addBlogPages(Sitemap $sitemap): void
    {
        $this->info('Adding blog index page...');

        $sitemap->add(
            Url::create(route('posts.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        );
    }

    /**
     * Add all published posts to sitemap
     */
    private function addPosts(Sitemap $sitemap): int
    {
        $this->info('Adding posts...');

        $postsCount = 0;

        Post::query()
            ->published()
            ->orderBy('updated_at', 'desc')
            ->chunk(100, function ($posts) use ($sitemap, &$postsCount) {
                foreach ($posts as $post) {
                    $sitemap->add(
                        Url::create(route('posts.show', $post))
                            ->setLastModificationDate($post->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
                    $postsCount++;
                }
            });

        $this->info("Added {$postsCount} posts");
        return $postsCount;
    }

    /**
     * Add all categories with published posts to sitemap
     */
    private function addCategories(Sitemap $sitemap): int
    {
        $this->info('Adding categories...');

        $categoriesCount = 0;

        Category::query()
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->chunk(50, function ($categories) use ($sitemap, &$categoriesCount) {
                foreach ($categories as $category) {
                    $sitemap->add(
                        Url::create(route('posts.category', $category))
                            ->setLastModificationDate($category->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                    $categoriesCount++;
                }
            });

        $this->info("Added {$categoriesCount} categories");
        return $categoriesCount;
    }

    /**
     * Add all tags with published posts to sitemap
     */
    private function addTags(Sitemap $sitemap): int
    {
        $this->info('Adding tags...');

        $tagsCount = 0;

        Tag::query()
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->chunk(50, function ($tags) use ($sitemap, &$tagsCount) {
                foreach ($tags as $tag) {
                    $sitemap->add(
                        Url::create(route('posts.tag', $tag))
                            ->setLastModificationDate($tag->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                    $tagsCount++;
                }
            });

        $this->info("Added {$tagsCount} tags");
        return $tagsCount;
    }

    /**
     * Add all authors with published posts to sitemap
     */
    private function addAuthors(Sitemap $sitemap): void
    {
        $this->info('Adding authors...');

        $authorsCount = 0;

        User::query()
            ->whereHas('posts', function ($query) {
                $query->published();
            })
            ->orderBy('updated_at', 'desc')
            ->chunk(50, function ($users) use ($sitemap, &$authorsCount) {
                foreach ($users as $user) {
                    $sitemap->add(
                        Url::create(route('posts.author', $user))
                            ->setLastModificationDate($user->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                            ->setPriority(0.5)
                    );
                    $authorsCount++;
                }
            });

        $this->info("Added {$authorsCount} authors");
    }
}
