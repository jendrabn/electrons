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

class SEOService
{
    protected string $siteName;
    protected string $defaultImage;
    protected string $logoPath;
    protected string $twitterHandle;

    public function __construct()
    {
        $this->siteName = config('app.name');
        $this->defaultImage = asset('images/default-og-image.jpg');
        $this->logoPath = asset('images/logo.png');
        $this->twitterHandle = '@yourtwitter';
    }

    /**
     * Set basic SEO meta tags with chaining
     */
    protected function setBasicSEO(string $title, string $description, string $canonical, array $keywords = []): self
    {
        SEOTools::setTitle($title)
            ->setDescription($description)
            ->setCanonical($canonical);

        SEOTools::metatags()
            ->addKeyword(['programming indonesia', 'tutorial coding', 'web development', 'mobile development', 'belajar programming', 'blog programming', 'developer indonesia', 'tutorial laravel', 'tutorial react', 'tutorial flutter', 'programming tips', 'coding bootcamp', 'software development'])
            ->addMeta('robots', 'index, follow')
            ->addMeta('author', $this->siteName)
            ->addMeta('revisit-after', '1 days')
            ->addMeta('language', 'Indonesian')
            ->addMeta('distribution', 'global')
            ->addMeta('rating', 'general')
            ->addMeta('theme-color', '#1a1a1a')
            ->addMeta('msapplication-navbutton-color', '#1a1a1a')
            ->addMeta('apple-mobile-web-app-status-bar-style', '#1a1a1a');

        if (!empty($keywords)) {
            SEOTools::metatags()->addKeyword($keywords);
        }

        return $this;
    }

    /**
     * Set Open Graph meta tags
     */
    protected function setOpenGraph(string $title, string $description, string $url, string $type = 'website', ?string $image = null): self
    {
        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setType($type)
            ->setSiteName($this->siteName);

        if ($image) {
            OpenGraph::addImage($image, ['height' => 630, 'width' => 1200]);
        } else {
            OpenGraph::addImage($this->defaultImage, ['height' => 630, 'width' => 1200]);
        }

        return $this;
    }

    /**
     * Set Twitter Card meta tags
     */
    protected function setTwitterCard(string $title, string $description, string $url, ?string $image = null): self
    {
        TwitterCard::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setType('summary_large_image')
            ->setSite($this->twitterHandle);

        if ($image) {
            TwitterCard::setImage($image);
        } else {
            TwitterCard::setImage($this->defaultImage);
        }

        return $this;
    }

    /**
     * Set basic JSON-LD schema
     */
    protected function setBasicJsonLd(string $title, string $description, string $type, string $url): self
    {
        JsonLd::setTitle($title)
            ->setDescription($description)
            ->setType($type)
            ->addValue('@context', 'https://schema.org')
            ->addValue('url', $url)
            ->addValue('name', $title)
            ->addValue('headline', $title)
            ->addValue('inLanguage', 'id-ID');

        return $this;
    }

    /**
     * Add publisher schema
     */
    protected function addPublisher(): self
    {
        JsonLd::addValue('publisher', [
            '@type' => 'Organization',
            'name' => $this->siteName,
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $this->logoPath,
                'width' => 200,
                'height' => 60
            ],
            'sameAs' => [
                'https://twitter.com/yourtwitter',
                'https://github.com/yourgithub',
                'https://linkedin.com/company/yourcompany'
            ]
        ]);

        return $this;
    }

    /**
     * Add breadcrumb schema
     */
    protected function addBreadcrumb(array $breadcrumbs): self
    {
        $breadcrumbItems = collect($breadcrumbs)->map(function ($breadcrumb, $index) {
            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        })->toArray();

        JsonLd::addValue('breadcrumb', [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems
        ]);

        return $this;
    }
}
