<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $with = ['heroMedia.asset'];

    protected $fillable = ['editorial_date', 'access_type', 'editorial_key', 'category_id', 'title', 'slug', 'excerpt', 'content', 'image', 'author', 'published_at', 'reading_time', 'is_featured', 'status', 'seo_title', 'meta_description', 'focus_keyword', 'secondary_keywords'];

    protected function casts(): array
    {
        return ['editorial_date' => 'date', 'published_at' => 'date', 'is_featured' => 'boolean', 'secondary_keywords' => 'array'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function mediaPlacements(): HasMany
    {
        return $this->hasMany(ArticleMediaPlacement::class)->orderBy('display_order');
    }

    public function heroMedia(): HasOne
    {
        return $this->hasOne(ArticleMediaPlacement::class)->where('position', 'hero');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->heroMedia?->asset) {
            return $this->heroMedia->asset->url;
        }

        if (! filled($this->image)) {
            return asset('images/articles/editorial-cover.svg');
        }

        return str_starts_with($this->image, 'storage/')
            ? asset($this->image)
            : asset('images/articles/'.$this->image);
    }

    public function getImageSrcsetAttribute(): ?string
    {
        return $this->heroMedia?->asset?->srcset;
    }

    public function getImageAltAttribute(): string
    {
        return match ($this->image) {
            'cont-facebook-instagram-spart.jpg' => 'Telefon cu alerte de conectare neobișnuită, lacăt roșu și scut de securitate',
            'telefon-microfon-privacy.jpg' => 'Telefon cu indicator de microfon, semnale de publicitate și scut roșu pentru confidențialitate',
            'ghid-cont-compromis.svg' => 'Ecran de recuperare a unui cont cu alerte de securitate și pași de protecție',
            'ghid-extensii-browser.svg' => 'Fereastră de browser cu extensii și un panou de verificare a permisiunilor',
            'ghid-permisiuni-telefon.svg' => 'Telefon cu setări pentru permisiunile de locație, cameră și microfon',
            default => 'Ilustrație pentru '.$this->title,
        };
    }

    public function getCoverIconAttribute(): string
    {
        return match ($this->slug) {
            'primele-30-minute-cont-compromis' => 'bi-stopwatch',
            'cum-elimini-extensii-suspecte-browser' => 'bi-puzzle',
            'cum-verifici-permisiuni-aplicatii-telefon' => 'bi-sliders',
            default => match ($this->category?->slug) {
                'fraude-online', 'stiri-alerte' => 'bi-exclamation-triangle',
                'date-personale', 'confidentialitate' => 'bi-person-lock',
                'parole-autentificare' => 'bi-key',
                'retele-sociale' => 'bi-people',
                'securitate-dispozitive' => 'bi-phone',
                'ghiduri-practice' => 'bi-journal-check',
                'inteligenta-artificiala' => 'bi-cpu',
                'securitate-financiara' => 'bi-credit-card',
                default => 'bi-shield-lock',
            },
        };
    }
}
