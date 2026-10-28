@extends('layouts.app')
@php($localizedArticle = app(\App\Support\LocalizedContent::class)->article($article))
@section('title', $localizedArticle['seo_title'].' — Securitate Digitală')
@section('description', $localizedArticle['meta_description'])
@section('og_type', 'article')
@php
    $coverIcon = $article->cover_icon;
    $heroMedia = $article->heroMedia;
    $articleOgImage = $heroMedia?->asset?->url ?? $article->image_url;
@endphp
@section('og_image', $articleOgImage)
@push('head')
@if($article->focus_keyword || $article->secondary_keywords)<meta name="keywords" content="{{ implode(', ', array_filter([$article->focus_keyword, ...($article->secondary_keywords ?? [])])) }}">@endif
<meta property="article:published_time" content="{{ $article->published_at->toAtomString() }}">
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $localizedArticle['title'],
    'description' => $localizedArticle['excerpt'],
    'image' => $articleOgImage,
    'datePublished' => $article->published_at->toAtomString(),
    'author' => ['@type' => 'Organization', 'name' => $article->author],
    'publisher' => ['@type' => 'Organization', 'name' => 'Securitate Digitală', 'url' => 'https://securitatedigitala.ro'],
    'mainEntityOfPage' => 'https://securitatedigitala.ro/articole/'.$article->slug,
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
@section('content')
<section class="article-hero container">
    <div class="article-hero-copy">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">{{ __('Home') }}</a><i class="bi bi-chevron-right"></i><a href="{{ route('articles.index') }}">{{ __('Articole') }}</a><i class="bi bi-chevron-right"></i><span>{{ $localizedArticle['category_name'] }}</span></nav>
        <x-category-badge :category="$article->category" />
        <h1>{{ $localizedArticle['title'] }}</h1>
        <p class="article-excerpt">{{ $localizedArticle['excerpt'] }}</p>
        <div class="author-meta"><span class="author-avatar"><i class="bi bi-shield-lock"></i></span><span><b>{{ $article->author }}</b><small><time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('j F Y') }}</time> · <x-reading-time :minutes="$article->reading_time" /></small></span></div>
    </div>
    <figure class="article-cover">
        @unless($heroMedia)<span class="article-cover-placeholder" aria-hidden="true"><i class="bi {{ $coverIcon }}"></i></span>@endunless
        <img src="{{ $articleOgImage }}" @if($heroMedia?->asset?->srcset)srcset="{{ $heroMedia->asset->srcset }}" sizes="(max-width: 767px) 100vw, 50vw"@endif alt="{{ $heroMedia ? (app()->isLocale('en') ? ($heroMedia->alt_text_en ?: $heroMedia->alt_text) : $heroMedia->alt_text) : (app()->isLocale('en') ? 'Illustration for '.$localizedArticle['title'] : $article->image_alt) }}" width="{{ $heroMedia?->asset?->width ?? 1200 }}" height="{{ $heroMedia?->asset?->height ?? 675 }}" fetchpriority="high" decoding="async" onerror="this.style.display='none'">
        @if($heroMedia && ($heroMedia->caption || $heroMedia->caption_en || $heroMedia->asset->photographer))<figcaption>@if($heroMedia->caption || $heroMedia->caption_en)<span class="article-photo-caption">{{ app()->isLocale('en') ? ($heroMedia->caption_en ?: $heroMedia->caption) : $heroMedia->caption }}</span>@endif @if($heroMedia->asset->photographer)<span class="article-photo-credit">{{ app()->isLocale('en') ? 'Photo' : 'Foto' }}: {{ $heroMedia->asset->photographer }}@if($heroMedia->asset->source_url), <a href="{{ $heroMedia->asset->source_url }}" target="_blank" rel="noopener noreferrer">{{ str_contains($heroMedia->asset->source_url, 'pexels.com') ? 'Pexels' : (app()->isLocale('en') ? 'source' : 'sursă') }}</a>@endif</span>@endif</figcaption>@endif
    </figure>
</section>
<div class="container article-layout">
    <article class="article-content">
        {!! app(\App\Services\ArticleMediaRenderer::class)->renderInline($localizedArticle['content'], $article->mediaPlacements->where('position', 'inline'), app()->isLocale('en')) !!}
        <x-security-callout>{{ __('Protecția datelor personale începe cu obiceiuri digitale simple, repetate consecvent.') }}</x-security-callout>
        @php($shareUrl = route('articles.show', $article))
        <div class="article-share" aria-label="{{ __('Distribuie articolul') }}">
            <span>{{ __('Distribuie articolul') }}</span>
            <div class="article-share-links">
                <a class="article-share-link facebook" href="https://www.facebook.com/sharer/sharer.php?{{ http_build_query(['u' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Distribuie pe Facebook') }}" title="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                <a class="article-share-link instagram" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Deschide Instagram pentru a distribui articolul') }}" title="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                <a class="article-share-link whatsapp" href="https://wa.me/?{{ http_build_query(['text' => $localizedArticle['title'].' '.$shareUrl]) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Distribuie pe WhatsApp') }}" title="WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
            </div>
        </div>
        <div class="article-end"><span>{{ __('Ți-a fost util acest ghid?') }}</span><a href="{{ route('articles.index') }}" class="text-link">{{ __('Descoperă și alte articole ') }}<i class="bi bi-arrow-right"></i></a></div>
    </article>
    <aside class="article-sidebar">
        <h2>{{ __('Articole recomandate') }}</h2>
        @foreach($recommended as $suggestion)<x-recommended-article :article="$suggestion" />@endforeach
        <div class="sidebar-cta"><i class="bi bi-shield-check"></i><h3>{{ __('Vrei mai multe ghiduri practice?') }}</h3><p>{{ __('Urmărește Securitate Digitală pentru articole noi despre confidențialitate, phishing, parole și siguranță online.') }}</p><a class="btn btn-primary" href="{{ route('articles.index') }}">{{ __('Vezi toate articolele') }}</a></div>
    </aside>
</div>
@endsection
