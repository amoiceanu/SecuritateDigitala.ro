<?php

namespace App\Services;

use App\Models\ArticleMediaPlacement;
use Illuminate\Support\Collection;

class ArticleMediaRenderer
{
    public function renderInline(string $content, Collection $placements, bool $english): string
    {
        if ($placements->isEmpty()) {
            return $content;
        }

        $byAnchor = collect();
        foreach ($placements as $placement) {
            foreach ([$placement->anchor_ro, $placement->anchor_en] as $anchor) {
                if (filled($anchor)) {
                    $byAnchor->put(trim($anchor), $placement);
                }
            }
        }

        return preg_replace_callback(
            '/(<h([1-6])\b[^>]*>.*?<\/h\2>)(.*?)(?=<h[1-6]\b|$)/is',
            function (array $match) use ($byAnchor, $english): string {
                $anchor = trim(html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $placement = $byAnchor->get($anchor);

                if (! $placement) {
                    return $match[0];
                }

                $asset = $placement->asset;
                $alt = e($english ? ($placement->alt_text_en ?: $placement->alt_text) : $placement->alt_text);
                $captionText = $english ? ($placement->caption_en ?: $placement->caption) : $placement->caption;
                $caption = filled($captionText) ? '<span class="article-photo-caption">'.e($captionText).'</span>' : '';
                $source = filled($asset->source_url) ? ', <a href="'.e($asset->source_url).'" target="_blank" rel="noopener noreferrer">'.(str_contains($asset->source_url, 'pexels.com') ? 'Pexels' : ($english ? 'source' : 'sursă')).'</a>' : '';
                $credit = filled($asset->photographer)
                    ? '<span class="article-photo-credit">'.($english ? 'Photo' : 'Foto').': '.e($asset->photographer).$source.'</span>'
                    : '';
                $figure = '<figure class="article-inline-media"><img src="'.e($asset->url).'"'.($asset->srcset ? ' srcset="'.e($asset->srcset).'" sizes="(max-width: 850px) 100vw, 850px"' : '').' width="'.$asset->width.'" height="'.$asset->height.'" alt="'.$alt.'" loading="lazy" decoding="async">'.($caption || $credit ? '<figcaption>'.$caption.$credit.'</figcaption>' : '').'</figure>';

                return $match[1].$match[3].$figure;
            },
            $content,
        ) ?? $content;
    }
}
