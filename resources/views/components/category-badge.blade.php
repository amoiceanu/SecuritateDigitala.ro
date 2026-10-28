@props(['category'])
@php($localizedCategory = app(\App\Support\LocalizedContent::class)->category($category))
<a class="category-badge" href="{{ route('articles.index', ['categorie' => $category->slug]) }}">{{ $localizedCategory['name'] }}</a>
