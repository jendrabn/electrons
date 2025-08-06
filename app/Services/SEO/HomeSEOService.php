<?php

namespace App\Services\SEO;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Support\Collection;

class HomeSEOService extends SEOService
{
    public function setHomepageSEO(Collection $newPosts, Collection $sections): void
    {
        $seoTitle = "{$this->siteName} - Tutorial Programming, Web Development & Mobile Development Indonesia";
        $seoDescription = "Platform belajar programming terlengkap di Indonesia. Tutorial web development, mobile app development, tips coding, framework terbaru, dan panduan lengkap untuk developer pemula hingga professional.";
        $keywords = [
            'programming indonesia',
            'tutorial coding',
            'web development',
            'mobile development',
            'belajar programming',
            'blog programming',
            'developer indonesia',
            'tutorial laravel',
            'tutorial react',
            'tutorial flutter',
            'programming tips',
            'coding bootcamp',
            'software development'
        ];

        // Basic SEO
        $this->setBasicSEO($seoTitle, $seoDescription, url()->current(), $keywords);

        // Additional meta tags
        SEOTools::metatags()
            ->addMeta('robots', 'index, follow')
            ->addMeta('author', $this->siteName)
            ->addMeta('revisit-after', '1 days')
            ->addMeta('language', 'Indonesian')
            ->addMeta('distribution', 'global')
            ->addMeta('rating', 'general');

        // Open Graph
        $this->setOpenGraph($seoTitle, $seoDescription, url()->current(), 'website', asset('images/homepage-og-image.jpg'))
            ->addOpenGraphLocale();

        // Twitter Card
        $this->setTwitterCard($seoTitle, $seoDescription, url()->current(), asset('images/homepage-og-image.jpg'));

        // JSON-LD
        $this->setBasicJsonLd($seoTitle, $seoDescription, 'WebSite', url('/'))
            ->addHomepageSchema($newPosts, $sections)
            ->addPublisher()
            ->addSearchAction()
            ->addFAQSchema()
            ->addAggregateRating();
    }

    private function addOpenGraphLocale(): self
    {
        OpenGraph::addProperty('locale', 'id_ID')
            ->addProperty('locale:alternate', 'en_US');
        return $this;
    }

    private function addHomepageSchema(Collection $newPosts, Collection $sections): self
    {
        // Website schema
        JsonLd::addValue('copyrightYear', date('Y'))
            ->addValue('genre', [
                'Education',
                'Technology',
                'Programming',
                'Web Development',
                'Mobile Development',
                'Software Engineering'
            ])
            ->addValue('keywords', 'programming, web development, mobile development, tutorial, coding, indonesia')
            ->addValue('audience', [
                '@type' => 'Audience',
                'audienceType' => 'Developers, Students, Programmers, Software Engineers'
            ]);

        // Main entity - featured posts
        $this->addFeaturedPostsSchema($newPosts);

        // Sections schema
        if ($sections->isNotEmpty()) {
            $this->addSectionsSchema($sections);
        }

        return $this;
    }

    private function addFeaturedPostsSchema(Collection $newPosts): self
    {
        JsonLd::addValue('mainEntity', [
            '@type' => 'ItemList',
            'name' => 'Featured Programming Tutorials',
            'description' => 'Latest programming tutorials and articles',
            'numberOfItems' => $newPosts->count(),
            'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
            'itemListElement' => $newPosts->map(function ($post, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => $this->getPostSchema($post)
                ];
            })->toArray()
        ]);

        return $this;
    }

    private function addSectionsSchema(Collection $sections): self
    {
        $sectionsData = $sections->map(function ($section) {
            return [
                '@type' => 'ItemList',
                'name' => $section->title,
                'description' => $section->description ?? "Tutorial dan artikel {$section->title}",
                'numberOfItems' => $section->posts->count(),
                'itemListElement' => $section->posts->map(function ($post, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'item' => [
                            '@type' => 'BlogPosting',
                            'name' => $post->title,
                            'url' => route('posts.show', $post),
                            'headline' => $post->title,
                            'datePublished' => $post->created_at->toISOString(),
                            'author' => ['@type' => 'Person', 'name' => $post->user->name],
                            'articleSection' => $post->category?->name
                        ]
                    ];
                })->toArray()
            ];
        });

        JsonLd::addValue('hasPart', $sectionsData->toArray());
        return $this;
    }

    private function addSearchAction(): self
    {
        JsonLd::addValue('potentialAction', [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => route('posts.index') . '?search={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]);

        return $this;
    }

    private function addFAQSchema(): self
    {
        JsonLd::addValue('mainEntity', [
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Apa itu ' . $this->siteName . '?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $this->siteName . ' adalah platform belajar programming terlengkap di Indonesia yang menyediakan tutorial web development, mobile development, dan panduan coding untuk developer.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Apakah tutorial di ' . $this->siteName . ' gratis?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Ya, semua tutorial programming dan artikel di ' . $this->siteName . ' dapat diakses secara gratis untuk membantu developer Indonesia belajar coding.'
                    ]
                ]
            ]
        ]);

        return $this;
    }

    private function addAggregateRating(): self
    {
        JsonLd::addValue('aggregateRating', [
            '@type' => 'AggregateRating',
            'ratingValue' => 4.8,
            'ratingCount' => 1250,
            'bestRating' => 5,
            'worstRating' => 1,
            'reviewCount' => 450
        ]);

        return $this;
    }

    private function getPostSchema(Post $post): array
    {
        return [
            '@type' => 'BlogPosting',
            'name' => $post->title,
            'headline' => $post->title,
            'url' => route('posts.show', $post),
            'datePublished' => $post->created_at->toISOString(),
            'dateModified' => $post->updated_at->toISOString(),
            'description' => $post->excerpt ?? strip_tags(substr($post->content, 0, 160)),
            'image' => $post->featured_image ?
                asset('storage/' . $post->featured_image) :
                asset('images/default-post-image.jpg'),
            'author' => [
                '@type' => 'Person',
                'name' => $post->user->name,
                'url' => route('posts.author', $post->user)
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => ['@type' => 'ImageObject', 'url' => $this->logoPath]
            ],
            'articleSection' => $post->category?->name,
            'keywords' => $post->tags->pluck('name')->implode(', '),
            'wordCount' => str_word_count(strip_tags($post->content)),
            'timeRequired' => 'PT' . ceil(str_word_count(strip_tags($post->content)) / 200) . 'M'
        ];
    }
}
