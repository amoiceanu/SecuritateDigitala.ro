@extends('layouts.app')
@section('title', __('Verifică expunerea datelor tale | Securitate Digitală'))
@section('description', __('Verifică dacă adresa ta de e-mail apare în breșe cunoscute și caută mențiuni publice ale datelor tale.'))
@push('head')<meta name="robots" content="noindex,nofollow">@endpush
@section('content')
<div class="container exposure-page personal-exposure-page">
    @if(session('exposure_notice'))
        <div class="exposure-alert" role="status" aria-live="polite"><i class="bi bi-info-circle" aria-hidden="true"></i><span>{{ __(session('exposure_notice')) }}</span></div>
    @endif
    @if($errors->any())
        <div class="exposure-alert exposure-alert--error" role="alert" aria-live="assertive">
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i><div><strong>{{ __('Verifică informațiile introduse') }}</strong><ul>@foreach($errors->all() as $error)<li>{{ __($error) }}</li>@endforeach</ul></div>
        </div>
    @endif

    <div class="exposure-main-grid">
        <div class="exposure-showcase">
            <section class="exposure-hero">
                <div class="exposure-hero-copy">
                    <span class="exposure-eyebrow">{{ __('PROTECȚIA DATELOR PERSONALE') }}</span>
                    <h1>{{ __('Unde sunt expuse') }}<br><span>{{ __('datele tale?') }}</span></h1>
                    <p>{{ __('Verifică dacă adresa ta de e-mail apare în breșe cunoscute și descoperă mențiuni publice ale numelui sau adresei tale pe internet.') }}</p>
                </div>
                <div class="exposure-illustration-wrap">
                    <img class="exposure-illustration" src="{{ asset('images/exposure/identity-shield.svg') }}" alt="" aria-hidden="true" width="620" height="420" fetchpriority="high">
                    <span class="exposure-illustration-note">{{ __('Ilustrație demonstrativă · fără rezultate reale') }}</span>
                </div>
                <div class="exposure-trust" aria-label="{{ __('Angajamente de confidențialitate') }}">
                    <div class="exposure-trust-item"><span class="exposure-trust-icon"><i class="bi bi-lock" aria-hidden="true"></i></span><span><strong>{{ __('Rapoarte private') }}</strong><small>{{ __('Doar tu vezi rezultatele.') }}</small></span></div>
                    <div class="exposure-trust-item"><span class="exposure-trust-icon"><i class="bi bi-clock" aria-hidden="true"></i></span><span><strong>{{ __('Retenție ') }}{{ $settings['retention_hours'] }}{{ __(' ore') }}</strong><small>{{ __('Raportul expiră automat.') }}</small></span></div>
                    <div class="exposure-trust-item"><span class="exposure-trust-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></span><span><strong>{{ __('Fără parole') }}</strong><small>{{ __('Nu este necesar un cont.') }}</small></span></div>
                </div>
                <div class="exposure-features">
                    @if($settings['breaches'])<a class="exposure-feature-card" href="#breaches">
                        <span class="exposure-feature-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                        <span class="exposure-feature-copy"><strong>{{ __('E-mail în breșe de date') }}</strong><small>{{ __('Verifică adresa în breșe cunoscute de date, raportate de servicii online.') }}</small></span>
                        <i class="bi bi-arrow-right exposure-feature-arrow" aria-hidden="true"></i>
                    </a>@else<div class="exposure-feature-card exposure-feature-card--disabled" aria-disabled="true"><span class="exposure-feature-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span><span class="exposure-feature-copy"><strong>{{ __('E-mail în breșe de date') }}</strong><small>{{ __('Verificarea adreselor de e-mail este momentan dezactivată.') }}</small></span></div>@endif
                    @if($settings['mentions'])<a class="exposure-feature-card" href="#mentions">
                        <span class="exposure-feature-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <span class="exposure-feature-copy"><strong>{{ __('Apariții publice pe internet') }}</strong><small>{{ __('Descoperă mențiuni în surse publice de pe internet.') }}</small></span>
                        <i class="bi bi-arrow-right exposure-feature-arrow" aria-hidden="true"></i>
                    </a>@else<div class="exposure-feature-card exposure-feature-card--disabled" aria-disabled="true"><span class="exposure-feature-icon"><i class="bi bi-search" aria-hidden="true"></i></span><span class="exposure-feature-copy"><strong>{{ __('Apariții publice pe internet') }}</strong><small>{{ __('Căutarea aparițiilor publice este momentan dezactivată.') }}</small></span></div>@endif
                </div>
            </section>
        </div>

        <aside id="breaches" class="exposure-panel exposure-email-card" aria-labelledby="breaches-title">
            <div class="exposure-panel-heading">
                <span class="exposure-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                <div><h2 id="breaches-title">{{ __('Verifică adresa de e-mail') }}</h2><p>{{ __('Află dacă adresa ta apare în breșe de date cunoscute.') }}</p></div>
            </div>
            @if(!$settings['breaches'])
                <div class="exposure-alert exposure-alert--warning" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><span>{{ __('Verificarea adreselor de e-mail este momentan dezactivată.') }}</span></div>
            @elseif(!$settingsService->configured('breaches') || !$settingsService->mailReady())
                <div class="exposure-alert exposure-alert--warning" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><span>{{ __('Furnizorul sau trimiterea confirmărilor nu este configurată. Verificarea nu a fost efectuată.') }}</span></div>
            @endif
            @if($settings['breaches'])
                <form method="post" action="{{ route('exposure.store') }}" class="exposure-form exposure-email-form" data-processing-form data-processing-text="{{ __('Se procesează…') }}" data-invalid-email="{{ __('Introdu o adresă de e-mail validă.') }}" data-invalid-address="{{ __('Verifică adresa de e-mail introdusă.') }}" data-email-check data-provider-ready="{{ $settingsService->configured('breaches') && $settingsService->mailReady() ? 'true' : 'false' }}">
                    @csrf
                    <input type="hidden" name="module" value="breaches"><input type="hidden" name="match_type" value="email">
                    <label for="breachEmail">{{ __('Adresa de e-mail') }}</label>
                    <div class="exposure-input-wrap"><i class="bi bi-envelope" aria-hidden="true"></i><input id="breachEmail" name="subject" type="email" maxlength="150" autocomplete="email" placeholder="nume@exemplu.ro" value="{{ old('subject', request('subject')) }}" required aria-describedby="breach-email-help breach-email-error"></div>
                    @error('subject')<p id="breach-email-error" class="exposure-field-error" role="alert">{{ $message }}</p>@else<p id="breach-email-error" class="exposure-field-error" aria-live="polite" hidden></p>@enderror
                    <label class="exposure-check"><input type="checkbox" name="own_data" value="1" required @checked(old('own_data'))><span>{{ __('Verific propria adresă de e-mail.') }}</span></label>
                    <button class="btn btn-primary exposure-submit" type="submit" @disabled(!$settingsService->configured('breaches') || !$settingsService->mailReady())><span>{{ __('Verifică adresa') }}</span><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                    <p id="breach-email-help" class="exposure-small exposure-confirmation-note"><i class="bi bi-lock" aria-hidden="true"></i> {{ __('Îți trimitem un link unic, valabil 15 minute. Rezultatele apar doar după confirmarea adresei.') }}</p>
                    <div class="exposure-trustbar" aria-label="{{ __('Protecția datelor') }}"><span><i class="bi bi-lock" aria-hidden="true"></i>{{ __('Rapoarte private') }}</span><span><i class="bi bi-clock" aria-hidden="true"></i>{{ $settings['retention_hours'] }} {{ __('ore') }}</span><span><i class="bi bi-shield-check" aria-hidden="true"></i>{{ __('Fără parole') }}</span></div>
                </form>
            @endif
            <div class="exposure-source-box">
                <i class="bi bi-globe2 exposure-source-globe" aria-hidden="true"></i>
                <div><strong>{{ __('Sursă: Have I Been Pwned (HIBP)') }}</strong><p>{{ __('Adresa este transmisă furnizorului numai după ce confirmi solicitarea.') }}</p><a href="https://haveibeenpwned.com/" target="_blank" rel="noopener noreferrer">{{ __('Despre HIBP') }} <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a></div>
            </div>
        </aside>
    </div>

    @if($settings['mentions'])
        <section id="mentions" class="exposure-panel exposure-mentions-card" aria-labelledby="mentions-title">
            <div class="exposure-secondary-heading"><div><span class="exposure-eyebrow">{{ __('CĂUTARE ÎN SURSE PUBLICE') }}</span><h2 id="mentions-title">{{ __('Apariții publice pe internet') }}</h2><p>{{ __('Verifică mențiuni publice ale numelui sau adresei tale. Această căutare folosește Brave Search.') }}</p></div><span class="exposure-icon"><i class="bi bi-globe2" aria-hidden="true"></i></span></div>
            @if(!$settingsService->configured('mentions'))<div class="exposure-alert exposure-alert--warning" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><span>{{ __('Brave Search nu este configurat. Verificarea nu a fost efectuată.') }}</span></div>@endif
            <form method="post" action="{{ route('exposure.store') }}" class="exposure-form exposure-public-form" data-public-search data-processing-form data-processing-text="{{ __('Se procesează…') }}" data-name-placeholder="{{ __('Numele și prenumele') }}" data-email-label="{{ __('Adresa ta de e-mail') }}" data-name-label="{{ __('Nume complet') }}" data-own-email-label="{{ __('Verific propria adresă de e-mail.') }}" data-own-name-label="{{ __('Confirm că verific propriile date.') }}" data-provider-ready="{{ $settingsService->configured('mentions') ? 'true' : 'false' }}">
                @csrf<input type="hidden" name="module" value="mentions">
                <fieldset class="exposure-choice"><legend>{{ __('Ce vrei să verifici?') }}</legend><label><input type="radio" name="match_type" value="name" @checked(old('match_type', 'name') === 'name')> {{ __('Numele meu') }}</label><label><input type="radio" name="match_type" value="email" @checked(old('match_type') === 'email')> {{ __('Adresa mea de e-mail') }}</label></fieldset>
                <div class="exposure-public-fields"><div><label for="publicSubject" data-subject-label>{{ __(old('match_type') === 'email' ? 'Adresa ta de e-mail' : 'Nume complet') }}</label><input id="publicSubject" name="subject" type="{{ old('match_type') === 'email' ? 'email' : 'text' }}" maxlength="150" placeholder="{{ old('match_type') === 'email' ? 'nume@exemplu.ro' : __('Numele și prenumele') }}" autocomplete="off" value="{{ old('subject') }}" required></div><div data-location @if(old('match_type') === 'email') hidden @endif><label for="publicLocation">{{ __('Localitate sau țară ') }}<span class="exposure-small">{{ __('(opțional)') }}</span></label><input id="publicLocation" name="location" maxlength="100" placeholder="{{ __('De exemplu, București') }}" autocomplete="off" value="{{ old('location') }}" @disabled(old('match_type') === 'email')></div><button class="btn btn-primary exposure-submit" type="submit" @disabled(!$settingsService->configured('mentions'))><span>{{ __('Caută mențiuni') }}</span><i class="bi bi-arrow-right" aria-hidden="true"></i></button></div>
                <label class="exposure-check"><input type="checkbox" name="own_data" value="1" required @checked(old('own_data'))><span data-own-label>{{ __(old('match_type') === 'email' ? 'Verific propria adresă de e-mail.' : 'Confirm că verific propriile date.') }}</span></label>
                <p class="exposure-small">{{ __('Datele introduse sunt transmise Brave Search. Adresa de e-mail trebuie confirmată înainte de căutare; confirmarea din această sesiune poate fi refolosită timp de o oră.') }}</p>
            </form>
            <div class="exposure-mentions-note"><i class="bi bi-shield-exclamation" aria-hidden="true"></i><p>{{ __('Rezultatele pot include persoane cu același nume. O mențiune publică nu reprezintă automat o scurgere de date. Căutarea acoperă rezultatele disponibile prin furnizor, nu toate paginile de pe internet.') }}</p></div>
        </section>
    @endif

    <section class="exposure-how" aria-labelledby="exposure-how-title">
        <div class="exposure-how-heading"><div><span class="exposure-eyebrow">{{ __('CUM FUNCȚIONEAZĂ') }}</span><h2 id="exposure-how-title">{{ __('3 pași simpli pentru rezultate') }}</h2></div><p>{{ __('Rapid, sigur și fără complicații.') }}</p></div>
        <ol class="exposure-steps">
            <li><span class="exposure-step-number">1</span><span class="exposure-step-icon"><i class="bi bi-window" aria-hidden="true"></i></span><div><strong>{{ __('Confirmi adresa') }}</strong><p>{{ __('Deschizi linkul primit în același browser.') }}</p></div></li>
            <li><span class="exposure-step-number">2</span><span class="exposure-step-icon"><i class="bi bi-database-check" aria-hidden="true"></i></span><div><strong>{{ __('Verificăm sursa') }}</strong><p>{{ __('Căutarea este procesată în siguranță, în fundal.') }}</p></div></li>
            <li><span class="exposure-step-number">3</span><span class="exposure-step-icon"><i class="bi bi-file-earmark-check" aria-hidden="true"></i></span><div><strong>{{ __('Primești pași clari') }}</strong><p>{{ __('Vezi datele raportate și recomandările potrivite.') }}</p></div></li>
        </ol>
    </section>

    <section class="exposure-bottom"><div><i class="bi bi-shield-lock" aria-hidden="true"></i><h2>{{ __('Datele tale rămân ale tale') }}</h2><p>{{ __('Raportul este legat de această sesiune, criptat și disponibil cel mult ') }}{{ $settings['retention_hours'] }}{{ __(' ore') }}{{ __('Îl poți șterge oricând. Nu publicăm căutările sau rezultatele.') }}</p></div><div><i class="bi bi-eye" aria-hidden="true"></i><h2>{{ __('O verificare, nu o garanție') }}</h2><p>{{ __('Lipsa rezultatelor nu dovedește lipsa expunerii. Nu calculăm un scor de siguranță și nu promitem ștergerea automată a datelor de pe internet.') }}</p></div></section>
    <p class="exposure-small exposure-retention">{{ __('Ștergerea din aplicație nu elimină datele din sistemele furnizorilor. Copiile de siguranță expiră conform politicii operatorului. ') }}<a href="{{ route('privacy') }}">{{ __('Politica de confidențialitate') }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></p>
</div>
@endsection
