@extends('layouts.app')
@section('title', 'Verifică expunerea datelor tale | Securitate Digitală')
@section('description', 'Verifică dacă adresa ta de e-mail apare în breșe cunoscute și caută mențiuni publice ale datelor tale.')
@push('head')<meta name="robots" content="noindex,nofollow">@endpush
@section('content')
<div class="container exposure-page personal-exposure-page">
    @if(session('exposure_notice'))
        <div class="exposure-alert" role="status" aria-live="polite"><i class="bi bi-info-circle" aria-hidden="true"></i><span>{{ session('exposure_notice') }}</span></div>
    @endif
    @if($errors->any())
        <div class="exposure-alert exposure-alert--error" role="alert" aria-live="assertive">
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i><div><strong>Verifică informațiile introduse</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        </div>
    @endif

    <div class="exposure-main-grid">
        <div class="exposure-showcase">
            <section class="exposure-hero">
                <div class="exposure-hero-copy">
                    <span class="exposure-eyebrow">PROTECȚIA DATELOR PERSONALE</span>
                    <h1>Unde sunt expuse<br><span>datele tale?</span></h1>
                    <p>Verifică dacă adresa ta de e-mail apare în breșe cunoscute și descoperă mențiuni publice ale numelui sau adresei tale pe internet.</p>
                </div>
                <div class="exposure-illustration-wrap">
                    <img class="exposure-illustration" src="{{ asset('images/exposure/identity-shield.svg') }}" alt="" aria-hidden="true" width="620" height="420" fetchpriority="high">
                    <span class="exposure-illustration-note">Ilustrație demonstrativă · fără rezultate reale</span>
                </div>
                <div class="exposure-trust" aria-label="Angajamente de confidențialitate">
                    <div class="exposure-trust-item"><span class="exposure-trust-icon"><i class="bi bi-lock" aria-hidden="true"></i></span><span><strong>Rapoarte private</strong><small>Doar tu vezi rezultatele.</small></span></div>
                    <div class="exposure-trust-item"><span class="exposure-trust-icon"><i class="bi bi-clock" aria-hidden="true"></i></span><span><strong>Retenție {{ $settings['retention_hours'] }} ore</strong><small>Raportul expiră automat.</small></span></div>
                    <div class="exposure-trust-item"><span class="exposure-trust-icon"><i class="bi bi-shield-check" aria-hidden="true"></i></span><span><strong>Fără parole</strong><small>Nu este necesar un cont.</small></span></div>
                </div>
                <div class="exposure-features">
                    @if($settings['breaches'])<a class="exposure-feature-card" href="#breaches">
                        <span class="exposure-feature-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                        <span class="exposure-feature-copy"><strong>E-mail în breșe de date</strong><small>Verifică adresa în breșe cunoscute de date, raportate de servicii online.</small></span>
                        <i class="bi bi-arrow-right exposure-feature-arrow" aria-hidden="true"></i>
                    </a>@else<div class="exposure-feature-card exposure-feature-card--disabled" aria-disabled="true"><span class="exposure-feature-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span><span class="exposure-feature-copy"><strong>E-mail în breșe de date</strong><small>Verificarea adreselor de e-mail este momentan dezactivată.</small></span></div>@endif
                    @if($settings['mentions'])<a class="exposure-feature-card" href="#mentions">
                        <span class="exposure-feature-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <span class="exposure-feature-copy"><strong>Apariții publice pe internet</strong><small>Descoperă mențiuni în surse publice de pe internet.</small></span>
                        <i class="bi bi-arrow-right exposure-feature-arrow" aria-hidden="true"></i>
                    </a>@else<div class="exposure-feature-card exposure-feature-card--disabled" aria-disabled="true"><span class="exposure-feature-icon"><i class="bi bi-search" aria-hidden="true"></i></span><span class="exposure-feature-copy"><strong>Apariții publice pe internet</strong><small>Căutarea aparițiilor publice este momentan dezactivată.</small></span></div>@endif
                </div>
            </section>
        </div>

        <aside id="breaches" class="exposure-panel exposure-email-card" aria-labelledby="breaches-title">
            <div class="exposure-panel-heading">
                <span class="exposure-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                <div><h2 id="breaches-title">Verifică adresa de e-mail</h2><p>Află dacă adresa ta apare în breșe de date cunoscute.</p></div>
            </div>
            @if(!$settings['breaches'])
                <div class="exposure-alert exposure-alert--warning" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Verificarea adreselor de e-mail este momentan dezactivată.</span></div>
            @elseif(!$settingsService->configured('breaches') || !$settingsService->mailReady())
                <div class="exposure-alert exposure-alert--warning" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Furnizorul sau trimiterea confirmărilor nu este configurată. Verificarea nu a fost efectuată.</span></div>
            @endif
            @if($settings['breaches'])
                <form method="post" action="{{ route('exposure.store') }}" class="exposure-form exposure-email-form" data-processing-form data-email-check data-provider-ready="{{ $settingsService->configured('breaches') && $settingsService->mailReady() ? 'true' : 'false' }}">
                    @csrf
                    <input type="hidden" name="module" value="breaches"><input type="hidden" name="match_type" value="email">
                    <label for="breachEmail">Adresa de e-mail</label>
                    <div class="exposure-input-wrap"><i class="bi bi-envelope" aria-hidden="true"></i><input id="breachEmail" name="subject" type="email" maxlength="150" autocomplete="email" placeholder="nume@exemplu.ro" value="{{ old('subject', request('subject')) }}" required aria-describedby="breach-email-help breach-email-error"></div>
                    @error('subject')<p id="breach-email-error" class="exposure-field-error" role="alert">{{ $message }}</p>@else<p id="breach-email-error" class="exposure-field-error" aria-live="polite" hidden></p>@enderror
                    <label class="exposure-check"><input type="checkbox" name="own_data" value="1" required @checked(old('own_data'))><span>Verific propria adresă de e-mail.</span></label>
                    <button class="btn btn-primary exposure-submit" type="submit" @disabled(!$settingsService->configured('breaches') || !$settingsService->mailReady())><span>Verifică adresa</span><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
                    <p id="breach-email-help" class="exposure-small exposure-confirmation-note"><i class="bi bi-lock" aria-hidden="true"></i> Îți trimitem un link unic, valabil 15 minute. Rezultatele apar doar după confirmarea adresei.</p>
                    <div class="exposure-trustbar" aria-label="Protecția datelor"><span><i class="bi bi-lock" aria-hidden="true"></i>Rapoarte private</span><span><i class="bi bi-clock" aria-hidden="true"></i>{{ $settings['retention_hours'] }} ore</span><span><i class="bi bi-shield-check" aria-hidden="true"></i>Fără parole</span></div>
                </form>
            @endif
            <div class="exposure-source-box">
                <i class="bi bi-globe2 exposure-source-globe" aria-hidden="true"></i>
                <div><strong>Sursă: Have I Been Pwned (HIBP)</strong><p>Adresa este transmisă furnizorului numai după ce confirmi solicitarea.</p><a href="https://haveibeenpwned.com/" target="_blank" rel="noopener noreferrer">Despre HIBP <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a></div>
            </div>
        </aside>
    </div>

    @if($settings['mentions'])
        <section id="mentions" class="exposure-panel exposure-mentions-card" aria-labelledby="mentions-title">
            <div class="exposure-secondary-heading"><div><span class="exposure-eyebrow">CĂUTARE ÎN SURSE PUBLICE</span><h2 id="mentions-title">Apariții publice pe internet</h2><p>Verifică mențiuni publice ale numelui sau adresei tale. Această căutare folosește Brave Search.</p></div><span class="exposure-icon"><i class="bi bi-globe2" aria-hidden="true"></i></span></div>
            @if(!$settingsService->configured('mentions'))<div class="exposure-alert exposure-alert--warning" role="status"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Brave Search nu este configurat. Verificarea nu a fost efectuată.</span></div>@endif
            <form method="post" action="{{ route('exposure.store') }}" class="exposure-form exposure-public-form" data-public-search data-processing-form data-provider-ready="{{ $settingsService->configured('mentions') ? 'true' : 'false' }}">
                @csrf<input type="hidden" name="module" value="mentions">
                <fieldset class="exposure-choice"><legend>Ce vrei să verifici?</legend><label><input type="radio" name="match_type" value="name" @checked(old('match_type', 'name') === 'name')> Numele meu</label><label><input type="radio" name="match_type" value="email" @checked(old('match_type') === 'email')> Adresa mea de e-mail</label></fieldset>
                <div class="exposure-public-fields"><div><label for="publicSubject" data-subject-label>{{ old('match_type') === 'email' ? 'Adresa ta de e-mail' : 'Nume complet' }}</label><input id="publicSubject" name="subject" type="{{ old('match_type') === 'email' ? 'email' : 'text' }}" maxlength="150" placeholder="{{ old('match_type') === 'email' ? 'nume@exemplu.ro' : 'Numele și prenumele' }}" autocomplete="off" value="{{ old('subject') }}" required></div><div data-location @if(old('match_type') === 'email') hidden @endif><label for="publicLocation">Localitate sau țară <span class="exposure-small">(opțional)</span></label><input id="publicLocation" name="location" maxlength="100" placeholder="De exemplu, București" autocomplete="off" value="{{ old('location') }}" @disabled(old('match_type') === 'email')></div><button class="btn btn-primary exposure-submit" type="submit" @disabled(!$settingsService->configured('mentions'))><span>Caută mențiuni</span><i class="bi bi-arrow-right" aria-hidden="true"></i></button></div>
                <label class="exposure-check"><input type="checkbox" name="own_data" value="1" required @checked(old('own_data'))><span data-own-label>{{ old('match_type') === 'email' ? 'Verific propria adresă de e-mail.' : 'Confirm că verific propriile date.' }}</span></label>
                <p class="exposure-small">Datele introduse sunt transmise Brave Search. Adresa de e-mail trebuie confirmată înainte de căutare; confirmarea din această sesiune poate fi refolosită timp de o oră.</p>
            </form>
            <div class="exposure-mentions-note"><i class="bi bi-shield-exclamation" aria-hidden="true"></i><p>Rezultatele pot include persoane cu același nume. O mențiune publică nu reprezintă automat o scurgere de date. Căutarea acoperă rezultatele disponibile prin furnizor, nu toate paginile de pe internet.</p></div>
        </section>
    @endif

    <section class="exposure-how" aria-labelledby="exposure-how-title">
        <div class="exposure-how-heading"><div><span class="exposure-eyebrow">CUM FUNCȚIONEAZĂ</span><h2 id="exposure-how-title">3 pași simpli pentru rezultate</h2></div><p>Rapid, sigur și fără complicații.</p></div>
        <ol class="exposure-steps">
            <li><span class="exposure-step-number">1</span><span class="exposure-step-icon"><i class="bi bi-window" aria-hidden="true"></i></span><div><strong>Confirmi adresa</strong><p>Deschizi linkul primit în același browser.</p></div></li>
            <li><span class="exposure-step-number">2</span><span class="exposure-step-icon"><i class="bi bi-database-check" aria-hidden="true"></i></span><div><strong>Verificăm sursa</strong><p>Căutarea este procesată în siguranță, în fundal.</p></div></li>
            <li><span class="exposure-step-number">3</span><span class="exposure-step-icon"><i class="bi bi-file-earmark-check" aria-hidden="true"></i></span><div><strong>Primești pași clari</strong><p>Vezi datele raportate și recomandările potrivite.</p></div></li>
        </ol>
    </section>

    <section class="exposure-bottom"><div><i class="bi bi-shield-lock" aria-hidden="true"></i><h2>Datele tale rămân ale tale</h2><p>Raportul este legat de această sesiune, criptat și disponibil cel mult {{ $settings['retention_hours'] }} ore. Îl poți șterge oricând. Nu publicăm căutările sau rezultatele.</p></div><div><i class="bi bi-eye" aria-hidden="true"></i><h2>O verificare, nu o garanție</h2><p>Lipsa rezultatelor nu dovedește lipsa expunerii. Nu calculăm un scor de siguranță și nu promitem ștergerea automată a datelor de pe internet.</p></div></section>
    <p class="exposure-small exposure-retention">Ștergerea din aplicație nu elimină datele din sistemele furnizorilor. Copiile de siguranță expiră conform politicii operatorului. <a href="{{ route('privacy') }}">Politica de confidențialitate <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></p>
</div>
@endsection
