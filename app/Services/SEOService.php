<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\User;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class SEOService
{
    protected string $siteName;
    protected string $defaultImage;
    protected string $logoPath;
    protected string $twitterHandle;
    protected array $defaultKeywords;
    protected string $inLanguage;
    protected array $sameAs;

    public function __construct()
    {
        $this->siteName      = (string) config('app.name', 'Electrons');
        $this->defaultImage  = asset('images/default-og-image.jpg');
        $this->logoPath      = asset('images/logo.png');
        $this->twitterHandle = '@ElectronsID';
        $this->inLanguage    = 'id-ID';
        $this->sameAs        = array_values(array_filter([
            'https://x.com/ElectronsID',
            'https://www.instagram.com/ElectronsID',
            'https://www.linkedin.com/company/electronsid',
            'https://www.youtube.com/@ElectronsID',
        ]));

        $this->defaultKeywords = [
            'tutorial coding',
            'belajar pemrograman',
            'web development',
            'mobile development',
            'laravel tutorial',
            'react tutorial',
            'nodejs tutorial',
            'ui ux design',
            'ci cd',
            'software testing',
            'komunitas developer',
            'komunitas pemrograman',
            'tanya jawab coding',
        ];
    }

    /* =========================
     * PUBLIC API
     * ======================= */

    public function setHomeSEO(Collection $newPosts, Collection $sections): void
    {
        $title       = "{$this->siteName} — Blog & Komunitas Pemrograman Indonesia";
        $description = 'Situs belajar dan komunitas teknologi: tutorial coding, panduan framework (Laravel, React, Flutter), tips pengembangan web & mobile, serta komunitas untuk diskusi dan tanya jawab developer.';
        $keywords    = $this->defaultKeywords;

        $this->applyBasicMeta($title, $description, $keywords, url('/'), $this->defaultImage);

        OpenGraph::setType('website')
            ->addProperty('locale', $this->inLanguage === 'id-ID' ? 'id_ID' : 'en_US')
            ->addProperty('locale:alternate', $this->inLanguage === 'id-ID' ? 'en_US' : 'id_ID');

        $baseUrl    = rtrim(url('/'), '/') . '/';
        $currentUrl = $baseUrl;

        $listItems = [];
        $position  = 1;
        foreach ($newPosts->take(10) as $post) {
            $postUrl = route('posts.show', $post); // {post:slug}
            $image   = $this->pickImage($post->image ?? null, $post->content ?? '') ?? $this->defaultImage;

            $listItems[] = [
                '@type'         => 'ListItem',
                'position'      => $position++,
                'url'           => $postUrl,
                'name'          => (string) $post->title,
                'image'         => $image,
                'datePublished' => optional($post->published_at)->toAtomString(),
            ];
        }

        $graph = [
            [
                '@type'       => 'CollectionPage',
                '@id'         => $baseUrl . '#collection',
                'url'         => $currentUrl,
                'name'        => $this->siteName . ' — Konten Informatif & Inspiratif Developer',
                'isPartOf'    => ['@id' => $baseUrl . '#website'],
                'about'       => ['@id' => $baseUrl . '#organization'],
                'description' => 'Kembangkan diri dengan konten informatif dan inspiratif seputar teknologi.',
                'breadcrumb'  => ['@id' => $baseUrl . '#breadcrumb'],
                'inLanguage'  => $this->inLanguage,
                'mainEntity'  => [
                    '@type' => 'ItemList',
                    '@id'   => $baseUrl . '#home-list',
                    'itemListElement' => $listItems,
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id'   => $baseUrl . '#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Blog', 'item' => route('posts.index')],
                ],
            ],
            [
                '@type'       => 'WebSite',
                '@id'         => $baseUrl . '#website',
                'url'         => $currentUrl,
                'name'        => $this->siteName,
                'description' => 'Konten Informatif dan Inspiratif Developer',
                'publisher'   => ['@id' => $baseUrl . '#organization'],
                'potentialAction' => [[
                    '@type'  => 'SearchAction',
                    'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $baseUrl . 'posts?search={search_term_string}'],
                    'query-input' => ['@type' => 'PropertyValueSpecification', 'valueRequired' => true, 'valueName' => 'search_term_string'],
                ]],
                'inLanguage' => $this->inLanguage,
            ],
            $this->organizationNode($baseUrl),
        ];

        JsonLd::setTitle($title)
            ->setSite($this->siteName)
            ->setDescription($description)
            ->setUrl($currentUrl)
            ->addValue('@context', 'https://schema.org')
            ->addValue('@graph', $graph);
    }

    public function setPostIndexSEO(?string $searchQuery = null, ?LengthAwarePaginator $paginator = null): void
    {
        if ($searchQuery) {
            $title       = "Hasil Pencarian: {$searchQuery} — {$this->siteName}";
            $description = "Hasil pencarian untuk \"{$searchQuery}\": kumpulan artikel, tutorial, dan diskusi terkait pemrograman dan pengembangan software.";
            $noindex     = true; // hasil pencarian sebaiknya noindex
        } else {
            $title       = "Artikel & Panduan — {$this->siteName}";
            $description = 'Kumpulan artikel dan tutorial: web & mobile development, best practice, tips coding, dan panduan framework untuk developer Indonesia.';
            $noindex     = false;
        }

        $this->applyBasicMeta($title, $description, $this->defaultKeywords, $this->buildCanonicalWithPage(), $this->defaultImage, $noindex);
        OpenGraph::setType('website');

        JsonLd::addValues([
            '@context'   => 'https://schema.org',
            '@type'      => 'CollectionPage',
            'name'       => $title,
            'headline'   => $title,
            'url'        => url()->current(),
            'inLanguage' => $this->inLanguage,
            'mainEntity' => $this->buildItemListFromPaginator($paginator),
        ]);

        $this->applyPaginationRel($paginator);
    }

    public function setCategorySEO(Category $category, ?LengthAwarePaginator $paginator = null): void
    {
        $title       = "Tutorial {$category->name} — {$this->siteName}";
        $description = "Kumpulan artikel dan tutorial tentang {$category->name}: panduan dari dasar hingga praktik lanjutan, contoh kode, dan studi kasus.";

        $keywords = array_values(array_unique(array_merge($this->defaultKeywords, [
            $category->name,
            "tutorial {$category->name}",
            "belajar {$category->name}",
            "{$category->name} programming",
            "{$category->name} development",
        ])));

        $this->applyBasicMeta($title, $description, $keywords, $this->buildCanonicalWithPage(), $this->defaultImage);
        OpenGraph::setType('website')->addProperty('article:section', $category->name);

        $bc = [
            ['Beranda', url('/')],
            [$category->name, route('posts.category', $category)],
        ];
        $this->writeBreadcrumbJsonLd($bc);
        $this->applyPaginationRel($paginator);

        $pageUrl = url()->current();
        $baseUrl = rtrim(url('/'), '/') . '/';

        JsonLd::addValues([
            '@context'   => 'https://schema.org',
            '@type'      => 'CollectionPage',
            '@id'        => $pageUrl . '#category',
            'name'       => $title,
            'url'        => $pageUrl,
            'isPartOf'   => ['@id' => $baseUrl . '#website'],
            'breadcrumb' => ['@id' => $pageUrl . '#breadcrumb'],
            'inLanguage' => $this->inLanguage,
            'about'      => ['@type' => 'Thing', 'name' => (string) $category->name],
            'mainEntity' => $this->buildItemListFromPaginator($paginator),
        ]);
    }

    public function setTagSEO(Tag $tag, ?LengthAwarePaginator $paginator = null): void
    {
        $title       = "Tag: {$tag->name} — {$this->siteName}";
        $description = "Artikel, tutorial, dan diskusi terkait {$tag->name}. Temukan panduan praktis, contoh kode, dan sharing pengalaman dari komunitas.";

        $keywords = array_values(array_unique(array_merge($this->defaultKeywords, [
            $tag->name,
            "artikel {$tag->name}",
            "tutorial {$tag->name}",
            "{$tag->name} programming",
        ])));

        $this->applyBasicMeta($title, $description, $keywords, $this->buildCanonicalWithPage(), $this->defaultImage);
        OpenGraph::setType('website');

        $bc = [['Beranda', url('/')], ["Tag: {$tag->name}", route('posts.tag', $tag)]];
        $this->writeBreadcrumbJsonLd($bc);
        $this->applyPaginationRel($paginator);

        $pageUrl = url()->current();
        $baseUrl = rtrim(url('/'), '/') . '/';

        JsonLd::addValues([
            '@context'   => 'https://schema.org',
            '@type'      => 'CollectionPage',
            '@id'        => $pageUrl . '#tag',
            'name'       => $title,
            'url'        => $pageUrl,
            'isPartOf'   => ['@id' => $baseUrl . '#website'],
            'breadcrumb' => ['@id' => $pageUrl . '#breadcrumb'],
            'inLanguage' => $this->inLanguage,
            'about'      => ['@type' => 'Thing', 'name' => (string) $tag->name],
            'mainEntity' => $this->buildItemListFromPaginator($paginator),
        ]);
    }

    public function setAuthorSEO(User $user, ?LengthAwarePaginator $paginator = null): void
    {
        $title       = "Artikel oleh {$user->name} — {$this->siteName}";
        $description = "Kumpulan tulisan oleh {$user->name}: tutorial, pengalaman pengembangan, dan insight teknis untuk developer.";

        $keywords = array_values(array_unique(array_merge($this->defaultKeywords, [
            $user->name,
            "artikel {$user->name}",
            "blog {$user->name}",
            'developer indonesia',
        ])));

        $this->applyBasicMeta($title, $description, $keywords, $this->buildCanonicalWithPage(), $this->defaultImage);

        OpenGraph::setType('profile')->addProperty('profile:username', $user->name);

        $pageUrl = url()->current();
        $baseUrl = rtrim(url('/'), '/') . '/';

        JsonLd::addValues([
            '@context'    => 'https://schema.org',
            '@type'       => 'ProfilePage',
            '@id'         => $pageUrl . '#author',
            'name'        => $title,
            'url'         => $pageUrl,
            'about'       => ['@type' => 'Person', 'name' => $user->name],
            'inLanguage'  => $this->inLanguage,
            'mainEntity'  => $this->buildItemListFromPaginator($paginator),
            'isPartOf'    => ['@id' => $baseUrl . '#website'],
        ]);

        $bc = [['Beranda', url('/')], ["Penulis: {$user->name}", $pageUrl]];
        $this->writeBreadcrumbJsonLd($bc);
        $this->applyPaginationRel($paginator);
    }

    public function setPostSEO(Post $post): void
    {
        $baseUrl    = rtrim(url('/'), '/') . '/';
        $url        = route('posts.show', $post); // {post:slug}
        $articleId  = $url . '#article';
        $imageId    = $url . '#primaryimage';
        $bcId       = $url . '#breadcrumb';
        $websiteId  = $baseUrl . '#website';
        $orgId      = $baseUrl . '#organization';

        $title       = Str::of((string)($post->seo_title ?? ''))->trim()->isNotEmpty()
            ? (string) $post->seo_title
            : ($post->title . ' - ' . $this->siteName);

        $description = $this->buildDescription($post->seo_description ?? null, $post->teaser ?? null, $post->content ?? '');
        $image       = $this->pickImage($post->image_url ?? null, $post->content ?? '') ?? $this->defaultImage;
        $thumb       = $image;

        $keywordsArr = method_exists($post, 'tags')
            ? (array) ($post->tags?->pluck('name')->all() ?? [])
            : [];

        $sectionsArr = array_values(array_unique(array_filter([
            $post->category->name ?? null,
            'Tutorial',
            'Programming',
        ])));

        $this->applyBasicMeta(
            $title,
            $description,
            array_values(array_unique(array_merge($this->defaultKeywords, $keywordsArr))),
            $url,
            $image
        );

        OpenGraph::setType('article')
            ->addProperty('article:published_time', optional($post->published_at)->toAtomString())
            ->addProperty('article:modified_time', optional($post->updated_at)->toAtomString())
            ->addProperty('article:author', $post->user->name ?? $this->siteName)
            ->addProperty('article:section', $post->category->name ?? 'Blog');

        foreach ($keywordsArr as $tag) {
            OpenGraph::addProperty('article:tag', (string) $tag);
        }

        TwitterCard::setType('summary_large_image')
            ->setSite($this->twitterHandle)
            ->setTitle($title)->setDescription($description)->setUrl($url)->setImage($image);

        $author         = $post->user;
        $personHash     = hash('sha256', 'person-' . ($author->id ?? $author->email ?? $author->name ?? 'unknown'));
        $personId       = $baseUrl . '#/schema/person/' . $personHash;
        $personImageId  = $baseUrl . '#/schema/person/image/';
        $authorImageUrl = $author->avatar ?? null; // kolom di DB

        $breadcrumbItems = array_values(array_filter([
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Blog',
                'item' => route('posts.index')
            ],
            $post->category?->name ? [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => (string) $post->category->name,
                'item' => route('posts.category', $post->category)
            ] : null,
            [
                '@type' => 'ListItem',
                'position' => $post->category?->name ? 3 : 2,
                'name' => $post->title
            ],
        ]));

        $graph = [
            [
                '@type' => 'Article',
                '@id'   => $articleId,
                'isPartOf'         => ['@id' => $url],
                'author'           => ['@id' => $personId],
                'headline'         => Str::limit($post->title, 110, ''),
                'datePublished'    => optional($post->published_at)->toAtomString(),
                'dateModified'     => optional($post->updated_at)->toAtomString(),
                'mainEntityOfPage' => ['@id' => $url],
                'publisher'        => ['@id' => $orgId],
                'image'            => ['@id' => $imageId],
                'thumbnailUrl'     => $thumb,
                'keywords'         => $keywordsArr,
                'articleSection'   => $sectionsArr,
                'inLanguage'       => $this->inLanguage,
                'about'            => $post->category?->name ? ['@type' => 'Thing', 'name' => (string) $post->category->name] : null,
            ],
            [
                '@type' => 'WebPage',
                '@id'   => $url,
                'url'   => $url,
                'name'  => $post->title . ' - ' . $this->siteName,
                'isPartOf'            => ['@id' => $websiteId],
                'primaryImageOfPage'  => ['@id' => $imageId],
                'image'               => ['@id' => $imageId],
                'thumbnailUrl'        => $thumb,
                'datePublished'       => optional($post->published_at)->toAtomString(),
                'dateModified'        => optional($post->updated_at)->toAtomString(),
                'description'         => $description,
                'breadcrumb'          => ['@id' => $bcId],
                'inLanguage'          => $this->inLanguage,
                'mainEntity'          => ['@id' => $articleId],
                'potentialAction'     => [['@type' => 'ReadAction', 'target' => [$url]]],
            ],
            [
                '@type'       => 'ImageObject',
                'inLanguage'  => $this->inLanguage,
                '@id'         => $imageId,
                'url'         => $image,
                'contentUrl'  => $image,
                'width'       => 1200,
                'height'      => 630,
                'caption'     => $post->image_caption ?: $post->title,
            ],
            ['@type' => 'BreadcrumbList', '@id' => $bcId, 'itemListElement' => $breadcrumbItems],
            [
                '@type'       => 'WebSite',
                '@id'         => $websiteId,
                'url'         => $baseUrl,
                'name'        => $this->siteName,
                'description' => 'Konten Informatif dan Inspiratif Developer',
                'publisher'   => ['@id' => $orgId],
                'potentialAction' => [[
                    '@type'  => 'SearchAction',
                    'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $baseUrl . 'posts?search={search_term_string}'],
                    'query-input' => ['@type' => 'PropertyValueSpecification', 'valueRequired' => true, 'valueName' => 'search_term_string'],
                ]],
                'inLanguage' => $this->inLanguage,
            ],
            $this->organizationNode($baseUrl),
            array_filter([
                '@type' => 'Person',
                '@id'   => $personId,
                'name'  => $author->name ?? 'Penulis',
                'image' => $authorImageUrl ? [
                    '@type'      => 'ImageObject',
                    'inLanguage' => $this->inLanguage,
                    '@id'        => $personImageId,
                    'url'        => $authorImageUrl,
                    'contentUrl' => $authorImageUrl,
                    'caption'    => $author->name ?? 'Penulis',
                ] : null,
                'sameAs' => array_values(array_filter([
                    $author->website  ?? null,
                    $author->facebook ?? null,
                    $author->twitter  ?? null,
                    $author->instagram ?? null,
                    $author->linkedin ?? null,
                ])),
                'url' => route('posts.author', $author),
            ]),
        ];

        JsonLd::setTitle($title)
            ->setSite($this->siteName)
            ->setDescription($description)
            ->setUrl($url)
            ->addValue('@context', 'https://schema.org')
            ->addValue('@graph', $graph);
    }

    /**
     * Set SEO for thread index (forum listing)
     */
    public function setThreadIndexSEO(?string $searchQuery = null, ?LengthAwarePaginator $paginator = null): void
    {
        if ($searchQuery) {
            $title       = "Hasil Pencarian Diskusi: {$searchQuery} — {$this->siteName}";
            $description = "Hasil pencarian diskusi untuk \"{$searchQuery}\": pertanyaan, jawaban, dan diskusi terkait pemrograman dari komunitas.";
            $noindex     = true;
        } else {
            $title       = "Diskusi & Komunitas — {$this->siteName}";
            $description = 'Komunitas tanya-jawab dan diskusi developer: ajukan pertanyaan, bantu jawaban, dan diskusikan masalah teknis bersama komunitas.';
            $noindex     = false;
        }

        $this->applyBasicMeta($title, $description, $this->defaultKeywords, $this->buildCanonicalWithPage(), $this->defaultImage, $noindex);
        OpenGraph::setType('website');

        JsonLd::addValues([
            '@context'   => 'https://schema.org',
            '@type'      => 'CollectionPage',
            'name'       => $title,
            'headline'   => $title,
            'url'        => url()->current(),
            'inLanguage' => $this->inLanguage,
            'mainEntity' => $this->buildThreadItemListFromPaginator($paginator),
        ]);

        $this->applyPaginationRel($paginator);
    }

    /**
     * Set SEO for single thread (show)
     */
    public function setThreadSEO(Thread $thread): void
    {
        $baseUrl = rtrim(url('/'), '/') . '/';
        $url     = route('comunity.show', $thread);

        $title = Str::of((string)($thread->title ?? ''))->trim()->isNotEmpty()
            ? (string) $thread->title . ' - ' . $this->siteName
            : ($this->siteName . ' — Diskusi');

        $description = Str::limit(strip_tags($thread->content ?? $thread->body ?? ''), 160, '');
        $image       = $this->pickImage($thread->image ?? null, $thread->content ?? '') ?? $this->defaultImage;

        $keywordsArr = method_exists($thread, 'tags') ? (array) ($thread->tags?->pluck('name')->all() ?? []) : [];

        $this->applyBasicMeta($title, $description, array_values(array_unique(array_merge($this->defaultKeywords, $keywordsArr))), $url, $image);

        OpenGraph::setType('article')
            ->addProperty('article:author', $thread->user->name ?? $this->siteName)
            ->addProperty('article:published_time', optional($thread->created_at)->toAtomString())
            ->addProperty('article:modified_time', optional($thread->updated_at)->toAtomString());

        TwitterCard::setType('summary_large_image')
            ->setSite($this->twitterHandle)
            ->setTitle($title)->setDescription($description)->setUrl($url)->setImage($image);

        $questionId = $url . '#question';

        $answersCount = method_exists($thread, 'comments') ? ($thread->comments?->count() ?? 0) : null;

        $graph = [
            [
                '@type'       => 'Question',
                '@id'         => $questionId,
                'name'        => $thread->title,
                'text'        => Str::limit(strip_tags($thread->content ?? $thread->body ?? ''), 300, ''),
                'answerCount' => $answersCount,
                'author'      => ['@type' => 'Person', 'name' => $thread->user->name ?? null],
                'dateCreated' => optional($thread->created_at)->toAtomString(),
                'inLanguage'  => $this->inLanguage,
            ],
            [
                '@type' => 'WebPage',
                '@id'   => $url,
                'url'   => $url,
                'name'  => $title,
                'isPartOf'  => ['@id' => $baseUrl . '#website'],
                'breadcrumb' => ['@id' => $url . '#breadcrumb'],
                'inLanguage' => $this->inLanguage,
                'mainEntity' => ['@id' => $questionId],
            ],
            $this->organizationNode($baseUrl),
        ];

        JsonLd::setTitle($title)
            ->setSite($this->siteName)
            ->setDescription($description)
            ->setUrl($url)
            ->addValue('@context', 'https://schema.org')
            ->addValue('@graph', $graph);
    }

    /**
     * Set SEO for create thread page (typically a form). Mark as noindex.
     */
    public function setThreadCreateSEO(?string $canonicalUrl = null): void
    {
        $title       = 'Buat Thread - ' . $this->siteName;
        $description = 'Buat thread baru untuk berdiskusi atau meminta bantuan. Berikan judul yang jelas dan jelaskan masalah Anda.';
        $canonical   = $canonicalUrl ?? url()->current();

        // mark create/edit forms as noindex
        $this->applyBasicMeta($title, $description, $this->defaultKeywords, $canonical, $this->defaultImage, true);

        OpenGraph::setType('website')->setUrl($canonical);

        JsonLd::addValues([
            '@context' => 'https://schema.org',
            '@type'    => 'WebPage',
            'name'     => $title,
            'url'      => $canonical,
            'inLanguage' => $this->inLanguage,
        ]);
    }

    /**
     * Set SEO for edit thread page (form). Mark as noindex.
     */
    public function setThreadEditSEO(Thread $thread, ?string $canonicalUrl = null): void
    {
        $title       = 'Edit: ' . ($thread->title ?? '') . ' - ' . $this->siteName;
        $description = 'Edit thread Anda — pastikan judul dan konten jelas agar komunitas dapat membantu.';
        $canonical   = $canonicalUrl ?? url()->current();

        $this->applyBasicMeta($title, $description, $this->defaultKeywords, $canonical, $this->defaultImage, true);

        OpenGraph::setType('website')->setUrl($canonical)->setTitle($title)->setDescription($description);

        JsonLd::addValues([
            '@context' => 'https://schema.org',
            '@type'    => 'WebPage',
            'name'     => $title,
            'url'      => $canonical,
            'inLanguage' => $this->inLanguage,
        ]);
    }

    /**
     * Build ItemList for threads (used in JSON-LD for index pages)
     */
    private function buildThreadItemListFromPaginator(?LengthAwarePaginator $paginator): ?array
    {
        if (!$paginator) return null;
        $items = [];
        $i = 1 + (($paginator->currentPage() - 1) * $paginator->perPage());
        foreach (collect($paginator->items())->take(10) as $thread) {
            $threadUrl = route('comunity.show', $thread);
            $image     = $this->pickImage($thread->image ?? null, $thread->content ?? '') ?? $this->defaultImage;
            $items[] = [
                '@type'         => 'ListItem',
                'position'      => $i++,
                'url'           => $threadUrl,
                'name'          => (string) ($thread->title ?? ''),
                'image'         => $image,
                'datePublished' => optional($thread->created_at)->toAtomString(),
            ];
        }
        return ['@type' => 'ItemList', 'itemListElement' => $items];
    }

    /* =========================
     * HELPERS (dipakai semua)
     * ======================= */

    private function applyBasicMeta(string $title, string $description, array $keywords, string $canonicalUrl, string $imageUrl, bool $noindex = false): void
    {
        $description = Str::limit(trim($description), 160, '');

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical($canonicalUrl);

        SEOTools::metatags()
            ->addKeyword($keywords)
            ->addMeta('author', $this->siteName)
            ->addMeta('language', $this->inLanguage === 'id-ID' ? 'Indonesian' : 'English')
            ->addMeta('robots', $noindex ? 'noindex,follow' : 'index,follow')
            ->addMeta('theme-color', '#1a1a1a')
            ->addMeta('msapplication-navbutton-color', '#1a1a1a')
            ->addMeta('apple-mobile-web-app-status-bar-style', '#1a1a1a');

        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($canonicalUrl)
            ->setSiteName($this->siteName)
            ->addImage($imageUrl, ['width' => 1200, 'height' => 630, 'alt' => $title])
            ->addProperty('og:image:secure_url', $imageUrl);

        TwitterCard::setTitle($title)->setDescription($description)->setUrl($canonicalUrl)->setImage($imageUrl);
    }

    private function writeBreadcrumbJsonLd(array $items): void
    {
        $list = [];
        $pos  = 1;
        foreach ($items as [$name, $itemUrl]) {
            $node = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $name];
            if (!empty($itemUrl)) $node['item'] = $itemUrl;
            $list[] = $node;
        }

        SEOTools::jsonLd()->addValues([
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => $list,
        ]);
    }

    private function buildCanonicalWithPage(): string
    {
        $current = url()->current();
        $page    = (int) request('page', 1);
        return $page > 1 ? ($current . '?page=' . $page) : $current;
    }

    private function applyPaginationRel(?LengthAwarePaginator $paginator): void
    {
        if (!$paginator) return;
        if (method_exists(SEOTools::metatags(), 'setPrev') && $paginator->previousPageUrl()) {
            SEOTools::metatags()->setPrev($paginator->previousPageUrl());
        }
        if (method_exists(SEOTools::metatags(), 'setNext') && $paginator->nextPageUrl()) {
            SEOTools::metatags()->setNext($paginator->nextPageUrl());
        }
    }

    private function buildDescription(?string $seo, ?string $teaser, string $content): string
    {
        if ($seo && Str::of($seo)->trim()->isNotEmpty()) return Str::limit(strip_tags($seo), 160, '');
        if ($teaser && Str::of($teaser)->trim()->isNotEmpty()) return Str::limit(strip_tags($teaser), 160, '');
        return Str::limit(strip_tags($content), 160, '');
    }

    private function pickImage(?string $image, string $content): ?string
    {
        if ($image && Str::of($image)->trim()->isNotEmpty()) return $this->safeUrl($image);
        $first = $this->firstImageFromHtml($content);
        return $first ?: $this->defaultImage;
    }

    private function firstImageFromHtml(string $html): ?string
    {
        if (!Str::contains($html, '<img')) return null;
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) {
            return $this->safeUrl($m[1]);
        }
        return null;
    }

    private function safeUrl(string $maybeUrl): string
    {
        return Str::startsWith($maybeUrl, ['http://', 'https://']) ? $maybeUrl : url($maybeUrl);
    }

    private function buildItemListFromPaginator(?LengthAwarePaginator $paginator): ?array
    {
        if (!$paginator) return null;
        $items = [];
        $i = 1 + (($paginator->currentPage() - 1) * $paginator->perPage());
        foreach (collect($paginator->items())->take(10) as $post) {
            /** @var Post $post */
            $postUrl = route('posts.show', $post);
            $image   = $this->pickImage($post->image ?? null, $post->content ?? '') ?? $this->defaultImage;
            $items[] = [
                '@type'         => 'ListItem',
                'position'      => $i++,
                'url'           => $postUrl,
                'name'          => (string) $post->title,
                'image'         => $image,
                'datePublished' => optional($post->published_at)->toAtomString(),
            ];
        }
        return ['@type' => 'ItemList', 'itemListElement' => $items];
    }

    private function organizationNode(string $baseUrl): array
    {
        return [
            '@type' => 'Organization',
            '@id'   => $baseUrl . '#organization',
            'name'  => $this->siteName,
            'url'   => $baseUrl,
            'logo'  => [
                '@type'      => 'ImageObject',
                'inLanguage' => $this->inLanguage,
                '@id'        => $baseUrl . '#/schema/logo/image/',
                'url'        => $this->logoPath,
                'contentUrl' => $this->logoPath,
                'width'      => 512,
                'height'     => 512,
                'caption'    => $this->siteName,
            ],
            'image'  => ['@id' => $baseUrl . '#/schema/logo/image/'],
            'sameAs' => $this->sameAs,
        ];
    }

    /* =========================
     * OPTIONAL SETTERS
     * ======================= */

    public function setLanguage(string $langCode): self
    {
        $this->inLanguage = $langCode;
        return $this;
    }
    /** @param array<int,string> $sameAs */
    public function setSameAs(array $sameAs): self
    {
        $this->sameAs = array_values(array_filter($sameAs));
        return $this;
    }
    public function setTwitterHandle(string $handle): self
    {
        $this->twitterHandle = $handle;
        return $this;
    }
    public function setLogo(string $url): self
    {
        $this->logoPath = $url;
        return $this;
    }
    public function setDefaultImage(string $url): self
    {
        $this->defaultImage = $url;
        return $this;
    }
}
