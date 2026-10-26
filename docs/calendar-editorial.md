# Calendar editorial

Calendarul este disponibil în meniul administrativ existent, **Calendar editorial**, la `/admin/calendar-editorial`. Utilizează Laravel 12, Blade/SCSS, autentificarea pe sesiune și middleware-urile `auth` + `admin` (`User::is_admin`). Nu introduce un alt model de articole, un al doilea editor, conturi sau permisiuni noi.

## Instalare și import

```sh
php artisan migrate
php artisan editorial:seed
npm run build
```

Comanda este dedicată acestui plan și apelează `EditorialCalendarSeeder`. Lista completă de 42 de titluri este în `database/data/editorial-plan-2026-2027.json`: trei articole/lună, noiembrie 2026–decembrie 2027, în zilele 5, 15 și 25; primele două public, al treilea premium. Nu este necesară rularea seederului general de catalog: acesta are propriile reguli de reorganizare a secțiunilor existente.

Importul este tranzacțional și folosește cheia editorială unică, stabilă `plan-YYYY-MM-N`. Rulările repetate nu dublează articolele și nu resetează modificările făcute de editori (inclusiv titlu, slug, dată, status sau conținut). Articolele anterioare importului nu sunt actualizate. Categoriile existente sunt refolosite după slug; secțiunea „Securitate online” folosește slugul `securitate-online-generala`, deoarece `securitate-online` este un alias vechi reorganizat automat de seederul de catalog. Categoriile lipsă sunt create fără modificarea celorlalte. Dacă un slug este deja ocupat de un alt articol, noul draft primește un sufix unic. Rerularea importului după ștergerea explicită a unui draft îl va recrea.

Toate înregistrările sunt create cu `status=draft`, `published_at=NULL`, `content=''`, `excerpt=''`, fără introduceri, rezumate, metadata SEO sau conținut generat. Timpul de citire are valoarea tehnică minimă 1 minut și poate fi modificat în editor. `editorial_key` este numai identificator de import, nu câmp editabil în formular.

## Utilizare

- Selectorul de lună și butoanele anterior/următor păstrează filtrele după categorie, acces și status.
- Calendarul afișează data propusă, titlul, categoria, Public/Premium și Draft/Publicat. Lista lunii este disponibilă pe desktop și este afișarea principală pe mobil.
- Apasă titlul sau **Editează** pentru formularul de articol deja existent. Modifică **Data propusă (calendar editorial)**, titlul, categoria, **Tip de acces editorial** și câmpurile de bază. Salvează și revii la calendarul lunii noii date.
- **Draft nou** folosește același formular, cu status draft implicit. Descrierea și conținutul pot rămâne goale cât timp articolul este draft.
- **Șterge** este disponibil în lista calendarului pentru drafturi, cu confirmare. Folosește ruta și permisiunea de ștergere deja existente.
- Articolele fără dată editorială rămân accesibile în lista generală de articole, fără a fi plasate automat în calendar. Articolele existente nu primesc retrospectiv o dată propusă.

## Separarea planificării de publicare

`editorial_date` este o coloană DATE, salvată ca `YYYY-MM-DD`, fără conversie la un instant UTC; ea reprezintă ziua editorială în Europe/Bucharest (`config/editorial.php`). Luna implicită și „astăzi” sunt calculate în același fus orar. Convenția existentă pentru `published_at` și timestampurile aplicației rămâne neschimbată.

Trecerea datei propuse nu schimbă niciodată statusul și nu populează data publicării. Nu există job, Scheduler sau comandă de publicare automată pentru calendar. Publicarea rămâne acțiunea manuală existentă: în formular, alege **Publicat**, completează conținutul și descrierea obligatorii și salvează. Câmpul separat **Data publicării** păstrează comportamentul existent pentru articole publicate cu dată viitoare; nu este completat din data editorială.

Etichetele `public` și `premium` sunt exclusiv informații editoriale. Nu există paywall și accesul vizitatorilor la articolele existente nu este schimbat. Protecția publică existentă după `status=published` rămâne activă în homepage, listări, căutare, categorii, recomandări, pagini individuale și sitemap, inclusiv dacă data editorială a unui draft este în trecut.

## Verificări

`php artisan test` și `npm run build`. Testele dedicate verifică importul celor 42 de înregistrări, distribuția lunară, conținutul gol, lipsa publicării, idempotenta după editare și coliziuni de slug, autorizarea, filtrele, navigarea, crearea/editarea/ștergerea drafturilor și protecția publică după trecerea datei editoriale. Nu apelați comanda de import pe producție decât în cadrul unei lansări autorizate.
