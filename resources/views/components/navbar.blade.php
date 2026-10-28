@php($sitePages = app(\App\Services\SitePages::class))
<header class="site-header">
    <nav class="navbar container" aria-label="{{ __('Navigare') }}">
        <a class="brand" href="{{ route('home') }}" aria-label="Securitate Digitală">
            <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
            <span>Securitate <b>Digitală</b></span>
        </a>
        <div class="nav-links" id="navLinks">
            @foreach($sitePages->navigation(['home', 'articles', 'guides', 'exposure', 'about', 'contact']) as $key => $page)
                @php($isActive = match($key) {
                    'home' => request()->routeIs('home'),
                    'articles' => request()->routeIs('articles.*') && ! in_array(request()->query('categorie'), ['ghiduri', 'ghiduri-practice'], true),
                    'guides' => request()->routeIs('articles.index') && in_array(request()->query('categorie'), ['ghiduri', 'ghiduri-practice'], true),
                    'exposure' => request()->routeIs('exposure.*'),
                    default => request()->routeIs($page['route']),
                })
                <a class="{{ $isActive ? 'active' : '' }}" href="{{ route($page['route'], $page['parameters'] ?? []) }}">{{ $page['nav_label'] }}</a>
            @endforeach
        </div>
        <div class="nav-tools">
            <div class="language-switch" aria-label="{{ __('Language') }}">
                <a href="{{ route('locale.switch', 'en') }}" lang="en" hreflang="en" aria-label="English" title="English" @if(app()->isLocale('en')) aria-current="page" @endif><img src="{{ asset('images/flags/gb.svg') }}" alt=""><span>EN</span></a>
                <a href="{{ route('locale.switch', 'ro') }}" lang="ro" hreflang="ro" aria-label="Română" title="Română" @if(app()->isLocale('ro')) aria-current="page" @endif><img src="{{ asset('images/flags/ro.svg') }}" alt=""><span>RO</span></a>
            </div>
            @if($sitePages->enabled('articles'))<button class="icon-button search-toggle" aria-label="{{ __('Caută articole') }}" type="button"><i class="bi bi-search" aria-hidden="true"></i></button>@endif
            <button class="icon-button menu-toggle" aria-label="{{ __('Deschide meniul') }}" data-label-open="{{ __('Deschide meniul') }}" data-label-close="{{ __('Închide meniul') }}" aria-expanded="false" aria-controls="navLinks" type="button"><i class="bi bi-list" aria-hidden="true"></i></button>
        </div>
    </nav>
</header>
