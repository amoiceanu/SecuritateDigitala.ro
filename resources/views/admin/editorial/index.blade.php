@extends('admin.layout')
@section('title','Calendar editorial — Administrare')
@section('content')
<div class="admin-heading"><div><h1>Calendar editorial</h1><p>Planificare lunară · {{ config('editorial.timezone') }}. Datele sunt propuneri, nu publicări automate.</p></div><a class="admin-button primary" href="{{ route('admin.articole.create',['calendar'=>1,'editorial_date'=>$month->format('Y-m').'-05']) }}"><i class="bi bi-plus-lg"></i> Draft nou</a></div>
<section class="admin-panel editorial-toolbar">
<form method="get" action="{{ route('admin.editorial.index') }}" class="admin-form editorial-filters">
 <div class="admin-field"><label for="month">Luna</label><input type="month" id="month" name="month" min="1900-01" max="2199-12" value="{{ $month->format('Y-m') }}" required></div>
 <div class="admin-field"><label for="calendar-category">Categorie</label><select id="calendar-category" name="category_id"><option value="">Toate categoriile</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(($filters['category_id']??'')==$category->id)>{{ $category->name }}</option>@endforeach</select></div>
 <div class="admin-field"><label for="calendar-access">Tip de acces</label><select id="calendar-access" name="access_type"><option value="">Toate tipurile</option><option value="public" @selected(($filters['access_type']??'')==='public')>Public</option><option value="premium" @selected(($filters['access_type']??'')==='premium')>Premium</option></select></div>
 <div class="admin-field"><label for="calendar-status">Status</label><select id="calendar-status" name="status"><option value="">Toate statusurile</option><option value="draft" @selected(($filters['status']??'')==='draft')>Draft</option><option value="published" @selected(($filters['status']??'')==='published')>Publicat</option></select></div>
 <button class="admin-button primary" type="submit">Aplică filtrele</button><a class="admin-button" href="{{ route('admin.editorial.index',['month'=>$month->format('Y-m')]) }}">Resetează</a>
</form>
<div class="editorial-month-nav">
 @if($month->format('Y-m')>'1900-01')<a class="admin-button" rel="prev" href="{{ route('admin.editorial.index',array_replace($filters,['month'=>$month->subMonth()->format('Y-m')])) }}"><i class="bi bi-chevron-left"></i> Luna anterioară</a>@endif
 <div><h2>{{ $month->locale('ro')->translatedFormat('F Y') }}</h2><span>{{ $articles->count() }} articole în selecție</span></div>
 @if($month->format('Y-m')<'2199-12')<a class="admin-button" rel="next" href="{{ route('admin.editorial.index',array_replace($filters,['month'=>$month->addMonth()->format('Y-m')])) }}">Luna următoare <i class="bi bi-chevron-right"></i></a>@endif
</div>
</section>
<p class="editorial-note"><a href="{{ route('admin.editorial.index',['month'=>'2026-11']) }}">Planul inițial: noiembrie 2026 – decembrie 2027</a></p>
<p class="editorial-note"><i class="bi bi-info-circle"></i> Drafturile rămân private și după data propusă. Publicarea se face manual din formularul articolului. Public/Premium sunt doar etichete editoriale.</p>
<div class="editorial-calendar" aria-label="Calendar pentru {{ $month->locale('ro')->translatedFormat('F Y') }}">
 @foreach(['Luni','Marți','Miercuri','Joi','Vineri','Sâmbătă','Duminică'] as $label)<div class="editorial-weekday">{{ $label }}</div>@endforeach
 @foreach($days as $day)<section class="editorial-day {{ $day->month!==$month->month?'outside':'' }} {{ $day->isSameDay(now(config('editorial.timezone')))?'today':'' }}"><time datetime="{{ $day->toDateString() }}">{{ $day->day }}</time>
 @if($day->month===$month->month)@foreach($byDate->get($day->toDateString(),collect()) as $article)<a class="editorial-event {{ $article->access_type==='premium'?'premium':'' }}" href="{{ route('admin.articole.edit',[$article,'calendar'=>1]) }}"><strong>{{ $article->title }}</strong><span>{{ $article->category->name }}</span><span class="editorial-badges"><span>{{ $article->access_type==='premium'?'Premium':'Public' }}</span><span>{{ $article->status==='draft'?'Draft':'Publicat' }}</span></span></a>@endforeach @endif
 </section>@endforeach
</div>
<section class="admin-panel editorial-list"><h2>Articolele lunii</h2><p class="editorial-list-hint">Lista oferă aceeași planificare într-un format potrivit pentru mobil.</p>
@forelse($articles as $article)<article class="editorial-list-item"><time datetime="{{ $article->editorial_date->format('Y-m-d') }}">{{ $article->editorial_date->format('d.m.Y') }}</time><div><h3><a href="{{ route('admin.articole.edit',[$article,'calendar'=>1]) }}">{{ $article->title }}</a></h3><p>{{ $article->category->name }} · {{ $article->access_type==='premium'?'Premium':'Public' }} · {{ $article->status==='draft'?'Draft':'Publicat' }}</p></div><div class="editorial-list-actions"><a class="admin-button" href="{{ route('admin.articole.edit',[$article,'calendar'=>1]) }}">Editează</a>@if($article->status==='draft')<form method="post" action="{{ route('admin.articole.destroy',$article) }}" onsubmit="return confirm('Ștergi acest draft? Acțiunea nu poate fi anulată.');">@csrf @method('DELETE')<button class="admin-button danger" type="submit">Șterge</button></form>@endif</div></article>@empty<p class="editorial-empty">Nu există articole cu dată propusă pentru luna și filtrele selectate. Poți crea un draft nou sau schimba filtrele.</p>@endforelse
</section>
@endsection
