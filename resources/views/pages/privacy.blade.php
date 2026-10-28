@extends('layouts.app')
@section('title', __('Politica de confidențialitate — Securitate Digitală'))
@section('description', __('Informații despre datele prelucrate când folosești securitatedigitala.ro.'))
@section('content')
<section class="page-hero container">
    <span class="eyebrow">{{ __('TRANSPARENȚĂ') }}</span>
    <h1>{{ __('Politica de ') }}<span>{{ __('confidențialitate') }}</span></h1>
    <p>{{ __('Informații despre datele prelucrate când folosești Securitate Digitală.') }}</p>
</section>
<section class="container section prose-page">
    <p><strong>{{ __('Notă:') }}</strong> {{ __('Acest text este un model informativ și nu reprezintă un document juridic definitiv. Adaptarea lui la situația operatorului și verificarea juridică sunt responsabilitatea acestuia.') }}</p>
    <h2>{{ __('Date colectate') }}</h2>
    <p>{{ __('Site-ul securitatedigitala.ro poate prelucra date tehnice strict necesare servirii paginilor. Dacă ne contactezi prin formular, colectăm numele, adresa de e-mail, subiectul și mesajul transmis, precum și momentul confirmării acordului.') }}</p>
    <h2>{{ __('Scop și temei') }}</h2>
    <p>{{ __('Folosim datele formularului pentru a citi și a răspunde solicitării tale, pe baza demersurilor cerute de tine și a interesului legitim de a gestiona comunicarea. Nu folosim datele pentru profilare sau publicitate.') }}</p>
    <h2>{{ __('Păstrare și securitate') }}</h2>
    <p>{{ __('Păstrăm mesajele atât cât este necesar pentru soluționarea solicitării și obligațiile aplicabile. Aplicăm măsuri rezonabile de acces limitat și securitate, însă nicio transmitere prin internet nu poate fi garantată complet.') }}</p>
    <h2>{{ __('Drepturile tale') }}</h2>
    <p>{{ __('În condițiile legii, poți solicita acces, rectificare, ștergere, restricționare sau opoziție și poți depune o plângere la autoritatea de supraveghere competentă. Pentru solicitări, folosește pagina ') }}<a href="{{ route('contact') }}">{{ __('Contact') }}</a>.</p>
    <h2>{{ __('Servicii terțe') }}</h2>
    <p>{{ __('Fonturile, componentele vizuale sau infrastructura pot fi furnizate de terți și pot primi date tehnice precum adresa IP. Pentru utilizare în producție, verifică furnizorii configurați și actualizează acest text cu datele operatorului și contactul său.') }}</p>
    <h2>{{ __('Verificarea datelor personale') }}</h2>
    <p>{{ __('Funcția opțională de verificare prelucrează datele proprii introduse de tine: adresa de e-mail sau numele și, opțional, localitatea. După confirmarea controlului adresei, aceasta este transmisă Have I Been Pwned pentru verificarea breșelor. Pentru mențiuni publice, termenii necesari sunt transmiși Brave Search. Nu cerem parole și nu descărcăm paginile rezultate. Consultă și politicile furnizorilor înainte de a continua.') }}</p>
    <p>{{ __('Datele și rezultatele rapoartelor sunt criptate în baza aplicației, accesibile doar sesiunii care a inițiat verificarea. Retenția implicită este de 24 de ore, configurabilă de operator între 1 și 72 de ore; termenul concret este afișat în pagină și raport. Poți șterge raportul înainte de expirare. La expirare accesul este blocat imediat, iar eliminarea fizică este făcută periodic de Scheduler. Pierderea cookie-ului de sesiune înseamnă pierderea accesului la raport.') }}</p>
    <p>{{ __('Clasificările „Sunt eu”, „Nu sunt eu” și „Nu sunt sigur” rămân în raportul privat și nu constituie verificări de identitate. Limitele antiabuz folosesc identificatori HMAC; statisticile administrative conțin numai totaluri pe zi și furnizor, păstrate 30 de zile. Ștergerea unui raport nu elimină datele de la furnizori sau din sursele publice. Backupurile sunt eliminate după ciclul de retenție al operatorului, nu instantaneu. Înainte de utilizare în producție, operatorul trebuie să precizeze durata backupurilor, furnizorii și eventualele transferuri internaționale în această politică.') }}</p>
    <h2>{{ __('Actualizări') }}</h2>
    <p>{{ __('Politica poate fi actualizată pentru a reflecta schimbări de funcționalitate sau cerințe legale. Verifică periodic această pagină.') }}</p>
</section>
@endsection
