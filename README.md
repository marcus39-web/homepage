# marcusreiser.de

Persoenliche Fotografie-Website von Marcus Reiser aus Weimar/Legefeld. Die Website praesentiert eine automatisch gepflegte Fotogalerie, einen Fotokalender und Projekte aus Fotografie und IT.

## Aufbau

Die Anwendung ist eine klassische PHP-Website ohne Build-Schritt. `index.php` ist der Frontcontroller: Er behandelt POST-Aktionen, prueft geschuetzte Statistikseiten, zaehlt Seitenaufrufe und laedt das passende Template aus `Components/pages/`. Gemeinsamer HTML-Rahmen und Navigation liegen in `Components/layout/`; das zentrale Styling ist `public/css/style.css`.

| Pfad | Zweck |
| --- | --- |
| `index.php` | Frontcontroller und URL-Routing |
| `bootstrap.php` | Sitzungen, ENV-Konfiguration und gemeinsame Helfer |
| `Components/pages/` | Startseite, Galerie, Kalender, Kontakt und Rechtliches |
| `Components/layout/` | Header, Navigation und Footer |
| `src/PhotoLibrary.php` | Fotoquellen, Kategorien, Dateipfade und Ausschlussregeln |
| `src/Router.php`, `src/View.php` | Vorhandene Platzhalter; das Routing verwendet aktuell direkt `index.php` |
| `public/photo.php` | Bild-Endpunkt mit Pfadpruefung, WebP-Cache und optionalen EXIF-Daten |
| `public/css/style.css` | Basislayout, Hero und responsive Gestaltung |
| `public/assets/css/style.css` | Mobile Navigation und Lazy-Load-Uebergaenge |
| `public/assets/js/` | Navigation, Lazy Loading und Galerie-Lightbox |
| `public/assets/images/` | Oeffentliche Web-Assets und ausgewaehlte Galeriebilder |
| `data/photos/` | Optionale Fotoquelle auf dem Webspace |
| `data/photo-cache/` | Vorab erzeugte WebP-Vorschauen und Galerievarianten |
| `data/logs/`, `data/messages/` | Besuchsstatistik, Kontaktanfragen und Kalenderanfragen |
| `.htaccess` | Clean URLs sowie Sperre interner Dateien/Verzeichnisse |

## Voraussetzungen

- PHP 8.1 oder neuer
- PHP-Erweiterung `mbstring` fuer die Formularvalidierung
- PHP-Erweiterung `exif` fuer Aufnahmezeit, GPS-Ort und Wetteranzeige (ohne EXIF laufen Galerie und Bilder weiter; es wird auf Dateizeit bzw. leere Metadaten zurueckgefallen)
- PHP-Erweiterung GD nur zum Erzeugen fehlender WebP-Varianten zur Laufzeit; vorhandene Cachedateien funktionieren ohne GD
- Fuer Orts- und Wetterinformationen muessen aus dem Browser externe Anfragen an Nominatim und Open-Meteo moeglich sein
- Apache mit `mod_rewrite` fuer den Produktivbetrieb
- Schreibrechte fuer `data/logs/` und `data/messages/`
- Python 3 und Pillow nur, wenn WebP-Varianten lokal vorab erzeugt werden sollen

## Lokal starten

Im Projektverzeichnis ausfuehren:

```powershell
php -S 127.0.0.1:8000 -t .
```

Anschliessend `http://127.0.0.1:8000/` im Browser oeffnen. Der eingebaute PHP-Server liest `.htaccess` nicht. `index.php` leitet deshalb vorhandene statische Dateien direkt durch und routet Seiten sowie `/public/photo.php` selbst. Nach Aenderungen an `php.ini` den Server neu starten.

## URLs und Funktionen

| Methode und URL | Funktion |
| --- | --- |
| `GET /` | Startseite und Ordner-Vorschauen |
| `GET /galerie` | Alle Kategorien und deren Unterordner |
| `GET /galerie?ordner=07_Blumen` | Nur die gewaehlte Kategorie |
| `GET /galerie?ordner=05_Weimar_und_Umgebung&unterordner=Tiefurt` | Einen Unterordner anzeigen; verschachtelte Pfade werden ebenfalls unterstuetzt |
| `GET /public/photo.php?category=...&file=...` | Bildauslieferung nach Pfad- und Dateityppruefung |
| `GET /kalender` | Kalenderblatt-Vorschau, zwölf bearbeitbare Monatsmotive im einheitlichen 4:3-Format, Bundesland-Feiertage, optional markierte Schulferien und Anfrageformular |
| `POST /kalender-bestellung` | Kalenderanfrage validieren und in `data/messages/orders.log` speichern |
| `GET /contact`, `POST /contact` | Kontaktformular mit CSRF-Pruefung, Datenschutz-Zustimmung und Honeypot |
| `GET /statistik-login`, `POST /statistik-login` | Anmeldung zum Statistikbereich |
| `GET /statistik` | Passwortgeschuetzte Besucherstatistik und Kalenderanfragen |
| `GET /statistik-logout` | Statistik abmelden |
| `GET /impressum`, `GET /datenschutz` | Rechtliche Informationsseiten |

Im Kalender lassen sich Wochenenden, gesetzliche Feiertage 2027 des gewaehlten Bundeslands und optional die Schulferien hervorheben. Das Bundesland und die Ferienoption werden lokal im Browser gespeichert. Die Ferienbereiche basieren auf den KMK-Ferienkalendern 2026/27 und 2027/28; bewegliche Ferientage und lokale Sonderregelungen sind nicht enthalten. Regionale Feiertage sind mit `*` gekennzeichnet.

## Fotogalerie und Bildablage

Der Scanner sucht die Fotoquelle in dieser Reihenfolge:

1. Der in `.env` konfigurierte Pfad `PHOTO_LIBRARY_PATH`, sofern er existiert.
2. `data/photos/` relativ zum Projektstamm. Dieser Pfad ist fuer den Webspace vorgesehen.
3. Das lokale Standardarchiv `D:/10_Fotoarchiv/Canon_R10_Bilder/01_Bibiothek_JPG`.

Die Kategorien sind direkte Unterordner der Quelle. Beispiel fuer netcup:

```text
R10/
└── data/
    └── photos/
        ├── 01_Gebaeude/
        │   └── Altstadt/Fassaden/Bild_01.JPG
        ├── 05_Weimar_und_Umgebung/
        │   └── Tiefurt/Ilm/Foto.JPG
        ├── 07_Blumen/
        │   └── Sonnenblumen/Legefeld/Foto.JPG
        └── 11_Weihnachten/
            └── Weihnachtsmarkt/Markt_01.JPG
```

Unterordner werden rekursiv gelesen. In der Galerie werden sie zuerst als Ordnerkarten angeboten; nach Auswahl erscheint nur der ausgewaehlte Zweig. Unterstuetzte Formate sind `.jpg`, `.jpeg`, `.png` und `.webp`. Leere Kategorien erscheinen nicht.

Ordner oder Unterordner, deren Name `Privat` enthaelt, werden sowohl beim Scannen als auch beim Bildabruf ausgeschlossen. Kategorien mit `Passbild` im Namen sowie reine Web-Ordner mit nummeriertem Namen und `Web` am Ende, zum Beispiel `20.02_Web`, werden nicht als Fotokategorien angezeigt. Lade nur Bilder hoch, die du auf der Website veroeffentlichen darfst; insbesondere keine RAW-Dateien, Zeugnisse, Passbilder oder privaten Aufnahmen.

`data/` wird durch `.htaccess` gegen direkten HTTP-Zugriff gesperrt. Bilder aus `data/photos/` werden daher ausschliesslich durch `public/photo.php` ausgeliefert. Der Endpunkt erlaubt nur die unterstuetzten Bildtypen und blockiert private Ordner, Passbild-Kategorien sowie Pfad-Traversal.

Bei `HEAD`-Anfragen liefert der Bild-Endpunkt EXIF-Daten im Header `X-Photo-Exif`. Die Lightbox zeigt daraus Aufnahmezeit und GPS-Ort an und fragt fuer GPS-Bilder historische bzw. aktuelle Stundenwerte bei Open-Meteo sowie Ortsnamen bei Nominatim ab. Verwendet wird der Wetterwert zur Aufnahmezeit in `Europe/Berlin`. Dafuer muessen EXIF-Daten vorhanden und externe Anfragen moeglich sein. GPS-Koordinaten oeffentlicher Bilder werden damit auch im HTTP-Header an Besucher ausgeliefert; veroeffentliche nur Fotos, deren Standortdaten du teilen moechtest.

### Optimierte Web-Fotos

Die Druck-Originale bleiben unveraendert. Vor dem Deployment erzeugt Pillow WebP-Dateien: Vorschauen haben maximal 800 Pixel Kantenlaenge bei Qualitaet 78, Galerievarianten maximal 1800 Pixel bei Qualitaet 82.

```powershell
python -m pip install Pillow
python tools/generate_photo_variants.py --source "D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG"
```

Die Varianten landen unter `data/photo-cache/preview/` und `data/photo-cache/gallery/`. Lade diesen Cache zusammen mit den benoetigten Originalen nach `data/` auf den Webspace. Fehlt eine Variante, versucht `public/photo.php` sie mit GD zu erzeugen; ist GD nicht verfuegbar oder schlaegt die Erzeugung fehl, wird das Original ausgeliefert. Fuer kurze Ladezeiten sollten die erzeugten Cache-Dateien mit deployed werden. Einzelne Bilder lassen sich zum Test mit `--match Marcus_Sonnenblumen_2.JPG` verarbeiten.

## Formulare und gespeicherte Daten

Kontakt- und Kalender-Anfrageformulare verwenden CSRF-Token, serverseitige Validierung, Datenschutz-Zustimmung und ein Honeypot-Feld. Kontaktanfragen werden lokal in `data/messages/contact.log` protokolliert und bei gesetztem `RESEND_API_KEY` ueber die Resend-API versendet; ohne API-Schluessel wird `mail()` als Server-Fallback verwendet. Fuer Resend muessen `RESEND_API_KEY` und eine auf der Domain verifizierte `RESEND_FROM_EMAIL` in `.env` gesetzt sein. Den API-Schluessel niemals committen oder weitergeben. Die Kalenderseite bietet eine Blattvorschau und eine Bildauswahl fuer jeden Monat. Die Auswahl wird im Browser gespeichert und mit der Anfrage uebermittelt. Kalenderanfragen werden zeilenweise als JSON in `data/messages/orders.log` gespeichert. Die Statistikseite zeigt Besucherzahlen sowie gespeicherte Anfragen inklusive Monatsmotiven; sensible IP-Daten werden in der Tabelle nicht angezeigt.

Der Besucherzaehler speichert Gesamt-, Pfad-, Tages- und Session-Tageswerte in `data/logs/visits.json`. Die interne Statistik zeigt Besucherzahlen fuer heute, eindeutige Besuche heute, den aktuellen Monat, insgesamt, den Verlauf der letzten 14 Tage und gespeicherte Kalenderanfragen.

## Konfiguration und Geheimnisse

`.env.example` enthaelt nur Beispielwerte. Kopiere sie lokal zu `.env` und setze eigene Werte. `.env` ist in `.gitignore` ausgeschlossen und darf nicht in Git eingecheckt oder zusammen mit Website-Dateien hochgeladen werden.

| Variable | Zweck |
| --- | --- |
| `APP_ENV` | Beispielwert in `.env.example`; wird vom aktuellen Anwendungscode nicht ausgewertet |
| `APP_NAME` | Beispielwert in `.env.example`; wird vom aktuellen Anwendungscode nicht ausgewertet |
| `STATS_PASSWORD` | Passwort fuer den internen Statistikbereich; nicht fuer den WCP-Verzeichnisschutz verwenden |
| `PHOTO_LIBRARY_PATH` | Optionaler absoluter Pfad zur Fotoquelle; auf netcup leer lassen, um `data/photos/` zu verwenden |
| `RESEND_API_KEY` | Geheim gehaltener API-Schluessel fuer den Versand von Kontaktanfragen ueber Resend |
| `RESEND_FROM_EMAIL` | Absenderadresse auf einer bei Resend verifizierten Domain, z. B. `info@marcusreiser.de` |

Fuer den Webspace bei Bedarf eine eigene `.env` in `R10/` ueber den Dateimanager anlegen. Dort ein neues, starkes `STATS_PASSWORD` setzen. Der separate Passwortschutz fuer die gesamte Testseite wird im netcup WCP verwaltet.

## Auf netcup testen

Die Testdomain ist `fotografie.marcusreiser.de`; das Webroot soll im WCP auf `httpdocs/R10` zeigen. Bei diesem Document Root wird die Website unter `https://fotografie.marcusreiser.de/` geoeffnet, nicht unter `/R10/`.

1. Im WCP den Verzeichnisschutz fuer den Webroot der Testdomain aktivieren und separat einen Benutzer anlegen.
2. Den Inhalt des Projekts nach `httpdocs/R10/` hochladen. `index.php`, `bootstrap.php`, `.htaccess`, `Components/`, `src/`, `public/` und benoetigte Dateien unter `data/` muessen an dieser Ebene liegen.
3. `public/css/style.css`, `public/photo.php` und `public/assets/images/marcus-reiser-logo.png` in ihrer Projektstruktur belassen.
4. Ausgewaehlte, oeffentliche Foto-Kategorien nach `R10/data/photos/` hochladen. Das Windows-Laufwerk `D:` ist vom Webserver nicht erreichbar.
5. Mit `python tools/generate_photo_variants.py --source "D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG"` die WebP-Varianten erstellen und `R10/data/photo-cache/` ebenfalls hochladen.
6. Eine Server-`.env` mit eigenem Statistikpasswort anlegen; lokale `.env`, `.git/`, `zugangslink.txt` und private Bilder nicht hochladen.
7. Pruefen, dass `data/logs/` und `data/messages/` durch PHP beschreibbar sind. Keine pauschalen `777`-Rechte vergeben.
8. In einem privaten Browserfenster `https://fotografie.marcusreiser.de/` oeffnen, den WCP-Zugang testen und Galerie, Unterordner, Kontaktformular sowie Kalenderseite pruefen.

Wenn Kategorien erscheinen, Bilder aber fehlen, zuerst die PHP-Fehlerprotokolle im WCP sowie die Aktualitaet von `src/PhotoLibrary.php` und `public/photo.php` pruefen. Ein HTTP-500 nach Klick auf einen Unterordner deutet typischerweise auf nicht zusammenpassende Versionen von `Components/pages/galerie.php` und `src/PhotoLibrary.php` hin.

## Vor der Veroeffentlichung

- Impressum und Datenschutzerklaerung mit den endgueltigen Angaben vervollstaendigen.
- Alle Galerie-, Unterordner-, Kontakt- und Bestellwege testen.
- Sicherstellen, dass keine privaten Aufnahmen im Fotoverzeichnis liegen.
- WCP-Testschutz entfernen oder passend anpassen, wenn die Website oeffentlich gehen soll.
- In netcup-Logs nach PHP-Fehlern sehen und Schreibrechte fuer Datenordner pruefen.
