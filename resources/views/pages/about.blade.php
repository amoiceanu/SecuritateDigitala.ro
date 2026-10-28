@extends('layouts.app')
@section('title', __('Despre Securitate Digitală'))
@section('description', __('Află despre proiectul educațional Securitate Digitală și resursele sale pentru protecția datelor personale și siguranță online.'))
@section('content')
<section class="page-hero container">
    <span class="eyebrow">{{ __('MISIUNEA NOASTRĂ') }}</span>
    <h1>{{ __('Despre') }} <span>Securitate Digitală</span></h1>
</section>
<section class="container section prose-page">
    <p>{{ __('Securitate Digitală este un proiect educațional dedicat protecției datelor personale și siguranței online.') }}</p>
    <p>{{ __('Scopul platformei este să explice într-un limbaj accesibil riscurile mediului digital și să ofere recomandări practice pe care oricine le poate aplica.') }}</p>
    <p><strong>{{ __('Protejează-ți viața online.') }}</strong></p>
    <x-security-callout>{{ __('Mai multă claritate înseamnă mai mult control asupra propriei vieți digitale.') }}</x-security-callout>
    <h2>{{ __('Notă informativă') }}</h2>
    <p>{{ __('Informațiile publicate au caracter educativ și informativ și nu reprezintă consultanță juridică sau de securitate informatică personalizată.') }}</p>
</section>
@endsection
