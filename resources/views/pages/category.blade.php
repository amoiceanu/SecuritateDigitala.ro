@extends('layouts.app')
@php($localizedCategory = app(\App\Support\LocalizedContent::class)->category($category))
@section('title', $localizedCategory['name'].' — Securitate Digitală')
@section('description', $localizedCategory['description'] ?: __('Articole și ghiduri despre ').$localizedCategory['name'].'.')
@section('canonical', 'https://securitatedigitala.ro'.route('sections.show', $category->slug, absolute: false))
@if(request()->filled('page'))@section('robots', 'noindex,follow')@endif
@section('content')
<section class="page-hero container">
    <span class="eyebrow"><a href="{{ route('articles.index') }}">{{ __('SECURITATE DIGITALĂ') }}</a></span>
    <h1>{{ $localizedCategory['name'] }}</h1>
    <p>{{ $localizedCategory['description'] ?: __('Ghiduri practice și informații utile despre ').$localizedCategory['name'].'.' }}</p>
</section>
<section class="container section article-listing">
    <p class="results-note">{{ $articles->total() }} {{ __($articles->total() === 1 ? 'articol publicat' : 'articole publicate') }}</p>
    <div class="article-grid">
        @forelse($articles as $article)<x-article-card :article="$article" />
        @empty<div class="empty-state"><h2>{{ __('Nu există articole publicate în această secțiune.') }}</h2></div>@endforelse
    </div>
    <div class="pagination-wrap">{{ $articles->links('pagination::bootstrap-5') }}</div>
</section>
@endsection
