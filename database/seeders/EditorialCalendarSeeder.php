<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EditorialCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(file_get_contents(database_path('data/editorial-plan-2026-2027.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                // A stable key survives title/date/slug edits; never update an existing article.
                if (Article::where('editorial_key', $row['key'])->exists()) {
                    continue;
                }
                $category = Category::firstOrCreate(['slug' => $row['category_slug']], ['name' => $row['category']]);
                $base = Str::slug($row['title']);
                $slug = $base;
                $suffix = 0;
                while (Article::where('slug', $slug)->exists()) {
                    $slug = $base.'-editorial-'.(++$suffix);
                }
                Article::firstOrCreate(['editorial_key' => $row['key']], [
                    'title' => $row['title'], 'slug' => $slug, 'category_id' => $category->id, 'access_type' => $row['access_type'],
                    'editorial_date' => $row['editorial_date'], 'status' => 'draft', 'excerpt' => '', 'content' => '',
                    'published_at' => null, 'author' => 'Redacția Securitate Digitală', 'reading_time' => 1, 'is_featured' => false,
                ]);
            }
        });
    }
}
