<?php

namespace App\Services\SEO;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\SEO\SEOService;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Support\Collection;

class PostSEOService extends SEOService
{
    public function setPostIndexSEO(?string $searchQuery = null): void
    {
        if ($searchQuery) {
            $seoTitle = "Hasil Pencarian: {$searchQuery} - Blog Programming & Development";
            $seoDescription = "Temukan artikel programming, web development, mobile development terkait '{$searchQuery}'. Tutorial, tips, dan panduan lengkap untuk developer.";
        } else {
            $seoTitle = "Blog Programming & Development - Tutorial Web, Mobile & Software Development";
            $seoDescription = "Blog programming Indonesia terlengkap. Tutorial web development, mobile app development, tips coding, framework terbaru, dan panduan developer profesional.";
        }

        $keywords = [
            'blog programming',
            'tutorial coding',
            'web development',
            'mobile development',
            'programming indonesia',
            'tutorial programming',
            'belajar coding',
            'developer indonesia',
            'software development',
            'programming tips'
        ];

        $this->setBasicSEO($seoTitle, $seoDescription, url()->current(), $keywords)
            ->setOpenGraph($seoTitle, $seoDescription, url()->current())
            ->setTwitterCard($seoTitle, $seoDescription, url()->current())
            ->setBasicJsonLd($seoTitle, $seoDescription, 'Blog', url()->current())
            ->addPublisher();
    }

    public function setCategorySEO(Category $category): void
    {
        $seoTitle = "Tutorial {$category->name} - Blog Programming & Development";
        $seoDescription = "Kumpulan artikel dan tutorial {$category->name} terlengkap. Pelajari {$category->name} dari dasar hingga advanced dengan panduan step-by-step.";
        $keywords = [
            $category->name,
            "tutorial {$category->name}",
            "belajar {$category->name}",
            "{$category->name} programming",
            "{$category->name} development",
            'programming indonesia',
            'tutorial programming'
        ];

        $this->setBasicSEO($seoTitle, $seoDescription, url()->current(), $keywords)
            ->setOpenGraph($seoTitle, $seoDescription, url()->current())
            ->setTwitterCard($seoTitle, $seoDescription, url()->current())
            ->setBasicJsonLd($seoTitle, $seoDescription, 'CollectionPage', url()->current())
            ->addPublisher()
            ->addBreadcrumb([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Blog', 'url' => route('posts.index')],
                ['name' => $category->name, 'url' => url()->current()]
            ]);

        $this->addCategorySchema($category);
    }

    public function setTagSEO(Tag $tag): void
    {
        $seoTitle = "Artikel dengan Tag {$tag->name} - Blog Programming";
        $seoDescription = "Temukan semua artikel programming yang berkaitan dengan {$tag->name}. Tutorial, tips, dan panduan development terbaru.";
        $keywords = [
            $tag->name,
            "artikel {$tag->name}",
            "tutorial {$tag->name}",
            "{$tag->name} programming",
            'blog programming',
            'programming indonesia'
        ];

        $this->setBasicSEO($seoTitle, $seoDescription, url()->current(), $keywords)
            ->setOpenGraph($seoTitle, $seoDescription, url()->current())
            ->setTwitterCard($seoTitle, $seoDescription, url()->current())
            ->setBasicJsonLd($seoTitle, $seoDescription, 'CollectionPage', url()->current())
            ->addPublisher();

        $this->addTagSchema($tag);
    }

    public function setAuthorSEO(User $user): void
    {
        $seoTitle = "Artikel oleh {$user->name} - Blog Programming";
        $seoDescription = "Baca semua artikel programming yang ditulis oleh {$user->name}. Tutorial, tips coding, dan pengalaman development dari author berpengalaman.";
        $keywords = [
            $user->name,
            "artikel {$user->name}",
            "blog {$user->name}",
            'programming author',
            'developer indonesia',
            'blog programming'
        ];

        $authorImage = $user->avatar ? asset('storage/' . $user->avatar) : null;

        $this->setBasicSEO($seoTitle, $seoDescription, url()->current(), $keywords)
            ->setOpenGraph($seoTitle, $seoDescription, url()->current(), 'profile', $authorImage)
            ->setTwitterCard($seoTitle, $seoDescription, url()->current(), $authorImage)
            ->setBasicJsonLd($seoTitle, $seoDescription, 'ProfilePage', url()->current())
            ->addPublisher();

        $this->addAuthorSchema($user);
    }

    public function setPostSEO(Post $post): void
    {
        $seoTitle = $post->title . ' - ' . $this->siteName;
        $seoDescription = $post->excerpt ?? strip_tags(substr($post->content, 0, 160)) . '...';
        $keywords = collect(['programming', 'tutorial', 'development'])
            ->merge($post->tags->pluck('name'))
            ->when($post->category, fn($collection) => $collection->push($post->category->name))
            ->unique()
            ->toArray();

        $featuredImage = $post->featured_image ?
            asset('storage/' . $post->featured_image) :
            asset('images/default-post-image.jpg');

        $this->setBasicSEO($seoTitle, $seoDescription, url()->current(), $keywords)
            ->setOpenGraph($post->title, $seoDescription, url()->current(), 'article', $featuredImage)
            ->setTwitterCard($post->title, $seoDescription, url()->current(), $featuredImage)
            ->setBasicJsonLd($post->title, $seoDescription, 'BlogPosting', url()->current())
            ->addPublisher();

        $this->addPostOpenGraphProperties($post)
            ->addPostSchema($post, $seoDescription)
            ->addPostBreadcrumb($post);
    }

    private function addCategorySchema(Category $category): self
    {
        JsonLd::addValue('about', [
            '@type' => 'Thing',
            'name' => $category->name,
            'description' => $category->description ?? "Tutorial dan artikel tentang {$category->name}"
        ]);

        return $this;
    }

    private function addTagSchema(Tag $tag): self
    {
        JsonLd::addValue('about', [
            '@type' => 'DefinedTerm',
            'name' => $tag->name,
            'description' => "Artikel yang berkaitan dengan {$tag->name}",
            'inDefinedTermSet' => [
                '@type' => 'DefinedTermSet',
                'name' => 'Programming Tags',
                'url' => route('posts.index')
            ]
        ]);

        return $this;
    }

    private function addAuthorSchema(User $user): self
    {
        JsonLd::addValue('mainEntity', [
            '@type' => 'Person',
            'name' => $user->name,
            'url' => url()->current(),
            'image' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'jobTitle' => 'Developer',
            'description' => $user->bio ?? "Programming author dan developer berpengalaman",
            'email' => $user->email,
            'knowsAbout' => ['Programming', 'Web Development', 'Software Engineering'],
            'worksFor' => [
                '@type' => 'Organization',
                'name' => $this->siteName,
                'url' => url('/')
            ],
            'sameAs' => array_filter([
                $user->twitter ? "https://twitter.com/{$user->twitter}" : null,
                $user->github ? "https://github.com/{$user->github}" : null,
                $user->linkedin ? "https://linkedin.com/in/{$user->linkedin}" : null,
                $user->website
            ])
        ]);

        return $this;
    }

    private function addPostOpenGraphProperties(Post $post): self
    {
        OpenGraph::addProperty('article:author', $post->user->name)
            ->addProperty('article:published_time', $post->created_at->toISOString())
            ->addProperty('article:modified_time', $post->updated_at->toISOString());

        if ($post->category) {
            OpenGraph::addProperty('article:section', $post->category->name);
        }

        foreach ($post->tags as $tag) {
            OpenGraph::addProperty('article:tag', $tag->name);
        }

        return $this;
    }

    private function addPostSchema(Post $post, string $seoDescription): self
    {
        $wordCount = str_word_count(strip_tags($post->content));
        $readingTime = ceil($wordCount / 200);

        JsonLd::addValue('headline', $post->title)
            ->addValue('description', $seoDescription)
            ->addValue('datePublished', $post->created_at->toISOString())
            ->addValue('dateModified', $post->updated_at->toISOString())
            ->addValue('wordCount', $wordCount)
            ->addValue('timeRequired', "PT{$readingTime}M")
            ->addValue('articleBody', strip_tags($post->content))
            ->addValue('articleSection', $post->category?->name)
            ->addValue('keywords', $post->tags->pluck('name')->implode(', '));

        // Author schema
        JsonLd::addValue('author', [
            '@type' => 'Person',
            'name' => $post->user->name,
            'url' => route('posts.author', $post->user),
            'image' => $post->user->avatar ? asset('storage/' . $post->user->avatar) : null,
            'jobTitle' => 'Developer',
            'description' => $post->user->bio ?? 'Programming author'
        ]);

        // Main entity
        JsonLd::addValue('mainEntityOfPage', [
            '@type' => 'WebPage',
            '@id' => url()->current(),
            'url' => url()->current()
        ]);

        // Image
        if ($post->featured_image) {
            JsonLd::addValue('image', [
                '@type' => 'ImageObject',
                'url' => asset('storage/' . $post->featured_image),
                'width' => 1200,
                'height' => 630,
                'caption' => $post->title
            ]);
        }

        return $this;
    }

    private function addPostBreadcrumb(Post $post): self
    {
        $breadcrumbItems = [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Blog', 'url' => route('posts.index')]
        ];

        if ($post->category) {
            $breadcrumbItems[] = [
                'name' => $post->category->name,
                'url' => route('posts.category', $post->category)
            ];
        }

        $breadcrumbItems[] = [
            'name' => $post->title,
            'url' => url()->current()
        ];

        $this->addBreadcrumb($breadcrumbItems);
        return $this;
    }
}
