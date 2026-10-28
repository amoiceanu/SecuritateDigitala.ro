<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleMediaPlacement extends Model
{
    protected $fillable = [
        'article_id', 'media_asset_id', 'position', 'display_order', 'anchor_ro', 'anchor_en', 'alt_text', 'alt_text_en', 'caption', 'caption_en',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }
}
