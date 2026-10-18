<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand" href="{{ route('home') }}" aria-label="Securitate Digitală">
                <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                <span>Securitate <b>Digitală</b></span>
            </a>
            <p>Protejează-ți viața online.</p>
            <a href="https://securitatedigitala.ro">securitatedigitala.ro</a>
        </div>
        <div>
            <h2>Navigare</h2>
            <a href="{{ route('home') }}">Home</a>
            @if(app(\App\Services\SitePages::class)->visible('articles'))<a href="{{ route('articles.index') }}">Articole</a>@endif
            <a href="{{ route('home') }}#sfatul-zilei">Sfatul zilei</a>
            @if(app(\App\Services\SitePages::class)->visible('guides'))<a href="{{ route('articles.index', ['categorie' => 'ghiduri-practice']) }}">Ghiduri</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('exposure'))<a href="{{ route('exposure.index') }}">Verifică datele</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('about'))<a href="{{ route('about') }}">Despre</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('contact'))<a href="{{ route('contact') }}">Contact</a>@endif
        </div>
        @if(app(\App\Services\SitePages::class)->visible('articles'))<div>
            <h2>Categorii</h2>
            <a href="{{ route('articles.index', ['categorie' => 'securitate-online']) }}">Securitate online</a>
            <a href="{{ route('articles.index', ['categorie' => 'phishing']) }}">Phishing</a>
            <a href="{{ route('articles.index', ['categorie' => 'date-personale']) }}">Date personale</a>
        </div>@endif
        <div>
            <h2>Informații</h2>
            <a href="{{ route('terms') }}">Termeni și condiții</a>
            @if(app(\App\Services\SitePages::class)->visible('privacy'))<a href="{{ route('privacy') }}">Politica de confidențialitate</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('cookies'))<a href="{{ route('cookies') }}">Politica cookies</a>@endif
            @if(app(\App\Services\SitePages::class)->visible('contact'))<a href="{{ route('contact') }}">Contact și solicitări</a>@endif
        </div>
    </div>
    <div class="container footer-bottom">
        <p>© {{ now()->year }} Securitate Digitală. Toate drepturile rezervate.</p>
        <p>Conținut educativ și informativ. Nu reprezintă consultanță juridică sau de securitate informatică personalizată.</p>
    </div>
</footer>
