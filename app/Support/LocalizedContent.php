<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class LocalizedContent
{
    public function article(Article $article): array
    {
        $english = app()->isLocale('en')
            ? (self::translations()['articles'][$article->slug] ?? [])
            : [];

        $category = $this->category($article->category);

        return [
            'title' => $english['title'] ?? $article->title,
            'excerpt' => $english['excerpt'] ?? $article->excerpt,
            'content' => $english['content'] ?? $article->content,
            'seo_title' => filled($english['seo_title'] ?? null) ? $english['seo_title'] : ($article->seo_title ?: ($english['title'] ?? $article->title)),
            'meta_description' => filled($english['meta_description'] ?? null) ? $english['meta_description'] : ($article->meta_description ?: ($english['excerpt'] ?? $article->excerpt)),
            'category_name' => $category['name'],
        ];
    }

    public function category(Category $category): array
    {
        $english = app()->isLocale('en')
            ? (self::translations()['categories'][$category->slug] ?? [])
            : [];

        return [
            'name' => $english['name'] ?? $category->name,
            'description' => $english['description'] ?? $category->description,
        ];
    }

    private static function translations(): array
    {
        $path = lang_path('en/site-content.json');
        $cacheKey = 'localized-content.en.'.(is_file($path) ? filemtime($path) : 'missing');

        return Cache::rememberForever($cacheKey, function () use ($path): array {

            if (! is_file($path)) {
                return [];
            }

            return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        });
    }
}
