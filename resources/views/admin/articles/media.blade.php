@extends('admin.layout')
@section('title', 'Imagini articol — Administrare')
@section('content')
<div class="admin-heading"><div><h1>Imagini articol</h1><p>{{ $article->title }}</p></div><a class="admin-button" href="{{ route('admin.articole.edit', $article) }}">Înapoi la articol</a></div>

@if(session('success'))<div class="admin-notice success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="admin-notice error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<section class="admin-panel">
    <h2>Adaugă fotografie sau imagine</h2>
    <p>Fotografiile sunt convertite în WebP și primesc versiuni responsive. Fișiere identice sunt stocate o singură dată.</p>
    <form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.articole.media.store', $article) }}">
        @csrf
        <div class="admin-form-grid">
            <div class="admin-field full"><label for="image">Fișier (JPG, PNG sau WebP, maximum 12 MB)</label><input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" required></div>
            <div class="admin-field"><label for="position">Poziție</label><select id="position" name="position" required><option value="inline">În articol</option><option value="hero">Imagine principală</option></select></div>
            <div class="admin-field"><label for="display_order">Ordine în articol</label><input id="display_order" name="display_order" type="number" min="0" value="1" required></div>
            <div class="admin-field"><label for="image_type">Tip imagine</label><select id="image_type" name="image_type" required><option value="photo">Fotografie</option><option value="screenshot">Captură de ecran</option><option value="mockup">Mockup</option><option value="ai">Generată cu AI</option></select></div>
            <div class="admin-field"><label for="photographer">Fotograf / autor</label><input id="photographer" name="photographer" maxlength="255"></div>
            <div class="admin-field"><label for="source_url">URL sursă originală</label><input id="source_url" name="source_url" type="url" maxlength="2048"></div>
            <div class="admin-field"><label for="license_name">Licență</label><input id="license_name" name="license_name" maxlength="120"></div>
            <div class="admin-field"><label for="license_url">URL licență</label><input id="license_url" name="license_url" type="url" maxlength="2048"></div>
            <div class="admin-field full"><label for="alt_text">Text alternativ (română)</label><input id="alt_text" name="alt_text" maxlength="500" required></div>
            <div class="admin-field full"><label for="alt_text_en">Alt text (English)</label><input id="alt_text_en" name="alt_text_en" maxlength="500"></div>
            <div class="admin-field full"><label for="anchor_ro">Titlul secțiunii după care apare (română)</label><input id="anchor_ro" name="anchor_ro" maxlength="180" placeholder="Lasă necompletat pentru imaginea principală"></div>
            <div class="admin-field full"><label for="anchor_en">Titlul secțiunii după care apare (engleză)</label><input id="anchor_en" name="anchor_en" maxlength="180"></div>
            <div class="admin-field full"><label for="caption">Legendă opțională</label><input id="caption" name="caption" maxlength="500"></div>
            <div class="admin-field full"><label for="caption_en">Optional caption (English)</label><input id="caption_en" name="caption_en" maxlength="500"></div>
        </div>
        <button class="admin-button primary" type="submit">Încarcă imaginea</button>
    </form>
</section>

<section class="admin-panel">
    <h2>Imagini asociate</h2>
    @forelse($article->mediaPlacements as $placement)
        <article class="admin-panel" style="margin-top:20px">
            <div class="admin-heading"><div><strong>{{ $placement->position === 'hero' ? 'Imagine principală' : 'Imagine în articol #'.$placement->display_order }}</strong><p>{{ $placement->asset->image_type }} · {{ $placement->asset->width }} × {{ $placement->asset->height }}</p></div><img src="{{ $placement->asset->url }}" alt="{{ $placement->alt_text }}" style="width:min(220px,35vw);height:120px;object-fit:cover;border-radius:8px"></div>
            @if($placement->asset->source_url)<p><a href="{{ $placement->asset->source_url }}" target="_blank" rel="noopener noreferrer">Sursa originală ↗</a> · {{ $placement->asset->photographer }} · {{ $placement->asset->license_name }}</p>@endif
            <form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.articole.media.update', [$article, $placement]) }}">
                @csrf @method('PUT')
                <div class="admin-form-grid">
                    <div class="admin-field full"><label>Înlocuiește fișierul (opțional)</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></div>
                    <div class="admin-field"><label>Poziție</label><select name="position"><option value="inline" @selected($placement->position === 'inline')>În articol</option><option value="hero" @selected($placement->position === 'hero')>Imagine principală</option></select></div>
                    <div class="admin-field"><label>Ordine</label><input name="display_order" type="number" min="0" value="{{ $placement->display_order }}" required></div>
                    <div class="admin-field"><label>Tip imagine</label><select name="image_type"><option value="photo" @selected($placement->asset->image_type === 'photo')>Fotografie</option><option value="screenshot" @selected($placement->asset->image_type === 'screenshot')>Captură de ecran</option><option value="mockup" @selected($placement->asset->image_type === 'mockup')>Mockup</option><option value="ai" @selected($placement->asset->image_type === 'ai')>Generată cu AI</option></select></div>
                    <div class="admin-field"><label>Fotograf / autor</label><input name="photographer" value="{{ $placement->asset->photographer }}" maxlength="255"></div>
                    <div class="admin-field"><label>URL sursă originală</label><input name="source_url" type="url" value="{{ $placement->asset->source_url }}" maxlength="2048"></div>
                    <div class="admin-field"><label>Licență</label><input name="license_name" value="{{ $placement->asset->license_name }}" maxlength="120"></div>
                    <div class="admin-field"><label>URL licență</label><input name="license_url" type="url" value="{{ $placement->asset->license_url }}" maxlength="2048"></div>
                    <div class="admin-field full"><label>Text alternativ</label><input name="alt_text" value="{{ $placement->alt_text }}" maxlength="500" required></div>
                    <div class="admin-field full"><label>Alt text (English)</label><input name="alt_text_en" value="{{ $placement->alt_text_en }}" maxlength="500"></div>
                    <div class="admin-field full"><label>Titlul secțiunii după care apare (română)</label><input name="anchor_ro" value="{{ $placement->anchor_ro }}" maxlength="180"></div>
                    <div class="admin-field full"><label>Section heading to place after (English)</label><input name="anchor_en" value="{{ $placement->anchor_en }}" maxlength="180"></div>
                    <div class="admin-field full"><label>Legendă opțională</label><input name="caption" value="{{ $placement->caption }}" maxlength="500"></div>
                    <div class="admin-field full"><label>Optional caption (English)</label><input name="caption_en" value="{{ $placement->caption_en }}" maxlength="500"></div>
                </div>
                <button class="admin-button primary" type="submit">Salvează</button>
            </form>
            <form method="post" action="{{ route('admin.articole.media.destroy', [$article, $placement]) }}" onsubmit="return confirm('Elimini imaginea din articol?');" style="margin-top:12px">
                @csrf @method('DELETE')<button class="admin-button danger" type="submit">Elimină din articol</button>
            </form>
        </article>
    @empty
        <p>Articolul nu are imagini gestionate prin media library.</p>
    @endforelse
</section>
@endsection
