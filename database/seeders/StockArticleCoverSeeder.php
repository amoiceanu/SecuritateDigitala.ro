<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\MediaAsset;
use Illuminate\Database\Seeder;
use RuntimeException;

class StockArticleCoverSeeder extends Seeder
{
    private const WIDTHS = [480, 768, 1200, 1280, 1600];

    public function run(): void
    {
        $covers = json_decode(file_get_contents(database_path('seeders/data/stock-article-covers.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($covers as $cover) {
            $article = Article::where('slug', $cover['slug'])->first();
            if (! $article) {
                // Test fixtures and partially seeded sites may not have every catalog article yet.
                continue;
            }

            // Keep covers uploaded or deliberately selected by an administrator.
            if ($article->heroMedia()->exists()) {
                continue;
            }

            $variants = [];
            foreach (self::WIDTHS as $width) {
                $path = "images/articles/stock/{$cover['slug']}-{$width}.webp";
                if (! is_file(public_path($path))) {
                    throw new RuntimeException("Missing stock photo variant: {$path}");
                }
                $variants[(string) $width] = $path;
            }

            $filePath = $variants['1280'];
            $asset = MediaAsset::firstOrCreate(['sha256' => hash_file('sha256', public_path($filePath))], [
                'file_path' => $filePath,
                'variants' => $variants,
                'mime_type' => 'image/webp',
                'width' => 1280,
                'height' => 720,
                'image_type' => 'photo',
                'source_url' => "https://www.pexels.com/photo/{$cover['photo_slug']}/",
                'photographer' => $cover['photographer'],
                'license_name' => 'Pexels License',
                'license_url' => 'https://www.pexels.com/license/',
            ]);

            $article->mediaPlacements()->create([
                'media_asset_id' => $asset->id,
                'position' => 'hero',
                'display_order' => 0,
                'alt_text' => $cover['alt_ro'],
                'alt_text_en' => $cover['alt_en'],
                'caption' => 'Fotografie ilustrativă. Credit: '.$cover['photographer'].' / Pexels.',
                'caption_en' => 'Illustrative photograph. Credit: '.$cover['photographer'].' / Pexels.',
            ]);

            $article->update(['image' => 'stock/'.$cover['slug'].'-1280.webp']);
        }

    }
}
