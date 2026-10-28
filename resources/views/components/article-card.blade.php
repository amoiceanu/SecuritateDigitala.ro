@props(['article'])
@php($localizedArticle = app(\App\Support\LocalizedContent::class)->article($article))
<article class="article-card">
    <a class="card-image" href="{{ route('articles.show', $article) }}" aria-label="{{ __('Citește: ') }}{{ $localizedArticle['title'] }}">
        <img src="{{ $article->image_url }}" @if($article->image_srcset)srcset="{{ $article->image_srcset }}" sizes="(max-width: 767px) 100vw, 33vw"@endif alt="{{ app()->isLocale('en') ? 'Photograph for '.$localizedArticle['title'] : $article->image_alt }}" width="{{ $article->heroMedia?->asset?->width ?? 800 }}" height="{{ $article->heroMedia?->asset?->height ?? 450 }}" loading="lazy" decoding="async" onerror="this.style.display='none'">
    </a>
    <div class="card-body">
        <x-category-badge :category="$article->category"/>
        <h3><a href="{{ route('articles.show', $article) }}">{{ $localizedArticle['title'] }}</a></h3>
        <p>{{ $localizedArticle['excerpt'] }}</p>
        <div class="meta"><time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('j F Y') }}</time><x-reading-time :minutes="$article->reading_time"/></div>
        <a class="text-link" href="{{ route('articles.show', $article) }}">{{ __('Citește articolul') }} <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
