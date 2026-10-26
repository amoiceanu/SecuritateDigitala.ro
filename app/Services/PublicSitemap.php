<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Collection;
use App\Services\SitePages;

class PublicSitemap
{
    private const ORIGIN = 'https://securitatedigitala.ro';

    /** @return Collection<int, array{loc:string,lastmod?:string}> */
    public function entries(): Collection
    {
        $pages = app(SitePages::class);
        $entries = collect();
        foreach (['home', 'articles', 'about', 'contact', 'privacy', 'cookies'] as $key) {
            if ($pages->enabled($key)) {
                $entries->push(['loc' => self::ORIGIN.route(config('site_pages.'.$key.'.route'), [], absolute: false)]);
            }
        }

        $categories = $pages->enabled('articles') ? Category::query()
            ->whereHas('articles', fn ($query) => $query->where('status', 'published')->whereNotNull('published_at')->whereDate('published_at', '<=', now()))
            ->withMax(['articles as content_updated_at' => fn ($query) => $query->where('status', 'published')->whereNotNull('published_at')->whereDate('published_at', '<=', now())], 'updated_at')
            ->orderBy('slug')
            ->get() : collect();
        foreach ($categories as $category) {
            if ($category->slug === 'ghiduri-practice' && ! $pages->enabled('guides')) {
                continue;
            }
            $entry = ['loc' => self::ORIGIN.route('sections.show', $category->slug, absolute: false)];
            if ($category->content_updated_at) {
                $entry['lastmod'] = Carbon::parse($category->content_updated_at)->toAtomString();
            }
            $entries->push($entry);
        }

        $articles = $pages->enabled('articles') ? Article::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereDate('published_at', '<=', now())
            ->orderBy('slug')
            ->get() : collect();
        foreach ($articles as $article) {
            $entry = ['loc' => self::ORIGIN.route('articles.show', $article->slug, absolute: false)];
            if ($article->updated_at) {
                $entry['lastmod'] = $article->updated_at->toAtomString();
            }
            $entries->push($entry);
        }

        return $entries->unique('loc')->values();
    }
}
