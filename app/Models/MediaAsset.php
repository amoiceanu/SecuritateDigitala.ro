<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    protected $fillable = [
        'file_path', 'sha256', 'variants', 'mime_type', 'width', 'height', 'image_type',
        'source_url', 'photographer', 'license_name', 'license_url',
    ];

    protected function casts(): array
    {
        return ['variants' => 'array'];
    }

    public function placements()
    {
        return $this->hasMany(ArticleMediaPlacement::class);
    }

    public function getUrlAttribute(): string
    {
        return str_starts_with($this->file_path, 'images/')
            ? asset($this->file_path)
            : Storage::disk('public')->url($this->file_path);
    }

    public function getSrcsetAttribute(): string
    {
        return collect($this->variants ?? [])
            ->map(fn (string $path, string|int $width): string => $this->publicUrl($path).' '.$width.'w')
            ->implode(', ');
    }

    public function publicUrl(string $path): string
    {
        return str_starts_with($path, 'images/')
            ? asset($path)
            : Storage::disk('public')->url($path);
    }

    public function deleteStoredFiles(): void
    {
        $paths = collect([$this->file_path, ...array_values($this->variants ?? [])])
            ->filter(fn (string $path): bool => ! str_starts_with($path, 'images/'))
            ->all();

        Storage::disk('public')->delete($paths);
    }
}
