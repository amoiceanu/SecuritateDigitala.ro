<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand" href="{{ route('home') }}" aria-label="Securitate Digitală">
                <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                <span>Securitate <b>Digitală</b></span>
            </a>
            <p>{{ __('Protejează-ți viața online.') }}</p>
            <a href="https://securitatedigitala.ro">securitatedigitala.ro</a>
        </div>
        <div>
            <h2>{{ __('Navigare') }}</h2>
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            @if(app(\App\Services\SitePages::class)->visible('articles'))<a href="{{ route('articles.index') }}">{{ __('Articole') }}</a>@endif
            <a href="{{ route('home') }}#sfatul-zilei">{{ __('Sfatul zilei') }}</a>
            @if(app(\App\Services\SitePages::class)->visible('guides'))<a href="{{ route('articles.index', ['categorie' => 'ghiduri-practice']) }}">{{ __('Ghiduri') }}</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('exposure'))<a href="{{ route('exposure.index') }}">{{ __('Verifică datele') }}</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('about'))<a href="{{ route('about') }}">{{ __('Despre') }}</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('contact'))<a href="{{ route('contact') }}">{{ __('Contact') }}</a>@endif
        </div>
        @if(app(\App\Services\SitePages::class)->visible('articles'))<div>
            <h2>{{ __('Categorii') }}</h2>
            <a href="{{ route('articles.index', ['categorie' => 'securitate-online']) }}">{{ __('Securitate online') }}</a>
            <a href="{{ route('articles.index', ['categorie' => 'phishing']) }}">Phishing</a>
            <a href="{{ route('articles.index', ['categorie' => 'date-personale']) }}">{{ __('Date personale') }}</a>
        </div>@endif
        <div>
            <h2>{{ __('Informații') }}</h2>
            <a href="{{ route('terms') }}">{{ __('Termeni și condiții') }}</a>
            @if(app(\App\Services\SitePages::class)->visible('privacy'))<a href="{{ route('privacy') }}">{{ __('Politica de confidențialitate') }}</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('cookies'))<a href="{{ route('cookies') }}">{{ __('Politica cookies') }}</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('contact'))<a href="{{ route('contact') }}">{{ __('Contact și solicitări') }}</a>@endif
        </div>
    </div>
    <div class="container footer-bottom">
        <p>© {{ now()->year }} Securitate Digitală. {{ __('Toate drepturile rezervate.') }}</p>
        <p>{{ __('Conținut educativ și informativ. Nu reprezintă consultanță juridică sau de securitate informatică personalizată.') }}</p>
    </div>
</footer>
