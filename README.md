# marcusreiser.de

Persoenliche Fotografie-Website von Marcus Reiser aus Legefeld – Hobbyfotograf und IT-Entwickler. Die Website praesentiert eine automatisch gepflegte Fotogalerie, einen Fotokalender und Projekte aus Fotografie und IT.

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
| `public/assets/images/` | Oeffentliche Web-Assets; das feste Startbild liegt unter `hero/` |
| `public/favicon.png` | Website-Icon mit einem Ausschnitt aus dem Startbild |
| `data/photos/` | Optionale Fotoquelle auf dem Webspace |
| `data/photo-cache/` | Vorab erzeugte WebP-Vorschauen und Galerievarianten |
| `data/logs/`, `data/messages/` | Besuchsstatistik, Kontaktanfragen und Kalenderanfragen |
| `.htaccess` | Clean URLs sowie Sperre interner Dateien/Verzeichnisse |

## Voraussetzungen

- PHP 8.1 oder neuer
- PHP-Erweiterung `mbstring` fuer die Formularvalidierung
- PHP-Erweiterung `exif` fuer Aufnahmezeit, GPS-Ort und Wetteranzeige (ohne EXIF laufen Galerie und Bilder weiter; es wird auf Dateizeit bzw. leere Metadaten zurueckgefallen)
- PHP-Erweiterung GD zum Erzeugen/Aktualisieren fehlender WebP-Varianten mit Wasserzeichen. Ohne GD funktionieren nur aktuell signierte Cachevarianten; fehlt eine verifizierte Variante, antwortet der Bild-Endpunkt mit HTTP 503 statt das Original ohne Wasserzeichen auszuliefern.
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
| `GET /` | Startseite, Ordner-Vorschauen und eigener Projektabschnitt fuer Immengoldkerzen mit Link zu `immengold.com` |
| `GET /galerie` | Ausschliesslich direkte Kategorien aus `20.02_Web` als Ordnerkarten; auch leere Ordner werden angezeigt |
| `GET /galerie?ordner=20.02_Web&unterordner=07_Blumen` | Bilder eines Web-Export-Ordners anzeigen; verschachtelte Unterordner werden unterstuetzt |
| `GET /galerie?ordner=20.02_Web&unterordner=13_Zwiebelmarkt` | Zwiebelmarkt-Bilder aus dem Web-Export anzeigen |
| `GET /public/photo.php?category=...&file=...` | Bildauslieferung nach Pfad- und Dateityppruefung |
| `GET /kalender` | Kalenderblatt-Vorschau, zwölf bearbeitbare Monatsmotive im einheitlichen 4:3-Format, Bundesland-Feiertage, optionale Schulferien und – bei aktiviertem Bestellschalter – Bestellformular |
| `POST /kalender-bestellung` | Kalenderanfrage validieren und in `data/messages/orders.log` speichern; nur bei `CALENDAR_ORDERS_ENABLED=true` aktiv |
| `POST /feedback` | Website-Feedback validieren, lokal speichern und per E-Mail weiterleiten |
| `GET /contact`, `POST /contact` | Kontaktformular mit CSRF-Pruefung, Datenschutz-Zustimmung und Honeypot |
| `GET /statistik-login`, `POST /statistik-login` | Anmeldung zum Statistikbereich |
| `GET /statistik` | Passwortgeschuetzte Besucherstatistik und Kalenderanfragen |
| `GET /statistik-logout` | Statistik abmelden |
| `GET /impressum`, `GET /datenschutz` | Rechtliche Informationsseiten |
| `GET /robots.txt` | Crawl-Regeln für Suchmaschinen und Sitemap-Verweis |
| `GET /sitemap.xml` | Sitemap der öffentlichen Hauptseiten |

Im Kalender lassen sich Wochenenden, gesetzliche Feiertage 2027 des gewaehlten Bundeslands und optional die Schulferien hervorheben. Das Bundesland und die Ferienoption werden lokal im Browser gespeichert. Die Ferienbereiche basieren auf den KMK-Ferienkalendern 2026/27 und 2027/28; bewegliche Ferientage und lokale Sonderregelungen sind nicht enthalten. Regionale Feiertage sind mit `*` gekennzeichnet.

## Google-Suche

`robots.txt` erlaubt das Crawling öffentlicher Seiten und verweist auf `https://marcusreiser.de/sitemap.xml`; interne Statistik- und POST-Endpunkte sind ausgeschlossen. Die Sitemap listet sechs öffentliche Hauptseiten. Canonical-URLs entfernen Trackingparameter und behalten bei der Galerie die ausgewählten Ordnerparameter.

Stand 07.10.2026: Die Property ist in der Google Search Console bestätigt; die Sitemap wurde ohne Fehler verarbeitet und sechs URLs wurden erkannt. Google-Suchen nach `site:marcusreiser.de` und „Marcus Reiser Weimar“ zeigen die Startseite und den Kalender. Die Kalenderseite erscheint auch bei „Weimar Wandkalender“ mit dem Titel „Weimar-Wandkalender 2027 – Marcus Reiser“. Google entscheidet weiterhin selbst über Indexierung, Suchausschnitt und Ranking.

Die Startseite enthält `Person`-JSON-LD mit Name, Website, Themen und öffentlichem Instagram-Profil. Ein PNG-Favicon mit einem Ausschnitt aus dem Startbild ist unter `/public/favicon.png` eingebunden; Google kann sein Suchtreffer-Symbol zeitversetzt aktualisieren.

## Fotogalerie und Bildablage

Die öffentliche Galerie, Kalenderauswahl und Fototeaser verwenden **ausschließlich** Bilder aus `20.02_Web`. Andere Hauptordner der Fotobibliothek werden weder angezeigt noch vom Bild-Endpunkt ausgeliefert. Das einzige Foto außerhalb des Exports ist das fest eingebundene Startbild `public/assets/images/hero/marcus-sonnenblumen.jpg`; das Markenlogo ist ein Gestaltungselement.

Die Fotoquelle wird in dieser Reihenfolge gesucht:

1. Der in `.env` konfigurierte Pfad `PHOTO_LIBRARY_PATH`, sofern er existiert.
2. `data/photos/` relativ zum Projektstamm; dies ist der vorgesehene Webspace-Pfad.
3. Das lokale Standardarchiv `D:/10_Fotoarchiv/Canon_R10_Bilder/01_Bibiothek_JPG`.

Innerhalb der gefundenen Quelle ist nur `20.02_Web/` freigegeben. Dessen direkte Unterordner werden automatisch als Galeriekategorien und Kalenderordner eingelesen. Neue Ordner und unterstützte Bilder (`.jpg`, `.jpeg`, `.png`, `.webp`) erscheinen ohne Codeänderung nach dem Upload. Leere Ordner werden mit 0 Bildern angezeigt; im Kalender sind sie sichtbar, aber ohne Motive nicht auswählbar.

Auf netcup lädt der FTP-Benutzer `foto-upload` nach `httpdocs/data/photos/`. Für die Website dürfen Bilder ausschließlich unter `httpdocs/data/photos/20.02_Web/<Kategorie>/...` abgelegt werden. Ordner oder Unterordner mit `Privat` im Namen sowie Pfade mit `Passbild` bleiben ausgeschlossen. Veröffentliche keine RAW-Dateien, Zeugnisse oder sonstigen privaten Aufnahmen.

`data/` ist durch `.htaccess` gegen direkten HTTP-Zugriff gesperrt. Bilder werden ausschließlich über `public/photo.php` ausgeliefert. Der Endpunkt akzeptiert nur die Kategorie `20.02_Web`, prüft unterstützte Dateitypen und blockiert Pfad-Traversal.

Bei `HEAD`-Anfragen liefert der Bild-Endpunkt EXIF-Daten im Header `X-Photo-Exif`. Die Galerie-Lightbox kann daraus Aufnahmezeit und GPS-Ort anzeigen und für GPS-Bilder Ortsnamen über Nominatim sowie Wetterwerte von Open-Meteo abrufen. Dafür müssen EXIF-Daten vorhanden und externe Anfragen möglich sein. GPS-Koordinaten öffentlicher Bilder werden im HTTP-Header an Besucher ausgeliefert; veröffentliche daher nur Fotos, deren Standortdaten du teilen möchtest.

### Optimierte Web-Fotos

Die Druck-Originale bleiben unveraendert. Vorschauen, Galerievarianten und Miniaturen werden als WebP mit Wasserzeichen erzeugt. Der Endpunkt akzeptiert eine Variante nur mit passendem `wm-v2`-Marker fuer Quelle und Wasserzeichen.

```powershell
python -m pip install Pillow
python tools/generate_photo_variants.py --source "D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG" --only-web-export
```

Der Cache liegt unter `data/photo-cache/` und enthält mit `--only-web-export` ausschliesslich Varianten fuer `20.02_Web`. Auf dem Webspace erzeugt `public/photo.php` fehlende oder veraltete Varianten beim ersten Abruf, sofern GD einschliesslich PNG- und WebP-Unterstuetzung verfuegbar ist. Schlaegt die Wasserzeichen-Erzeugung fehl oder fehlt eine gueltige Cache-Signatur, liefert der Endpunkt HTTP 503 statt ein unmarkiertes Original auszugeben. Ohne GD muessen die Varianten mit Pillow vorab erzeugt und nach `httpdocs/data/photo-cache/` hochgeladen werden. Die JPG-Originale werden nie veraendert.

Die Galerie und die Kalenderauswahl lesen dynamisch alle direkten Ordner aus `data/photos/20.02_Web/`, darunter `09_Instrumente` und `12_Kerzen_Immengold_Isabelle_Kraemer`. Neue Ordner und Bilder erscheinen nach dem Upload automatisch; leere Ordner werden angezeigt, aber haben noch keine auswählbaren Motive.

Der Projektabschnitt `Immengoldkerzen` auf der Startseite verwendet ein Bild aus `20.02_Web/12_Kerzen_Immengold_Isabelle_Kraemer/` und verlinkt auf `https://immengold.com`. Das Hero-Startbild bleibt als einzige Fotoausnahme ein festes Asset unter `public/assets/images/hero/`.

Das Wasserzeichen erschwert eine unveraenderte Weiterverwendung, verhindert aber keine Screenshots oder das Speichern eines im Browser angezeigten Bildes.

Der Footer zeigt den Gesamtwert der Seitenaufrufe aus `data/logs/visits.json`. Der Zähler speichert selbst keine IP-Adresse.

## Formulare und gespeicherte Daten

Kontakt- und Feedbackformulare begrenzen gültige Absendeversuche auf fünf pro IP-Adresse und Stunde. Ein HMAC der IP-Adresse und Zeitstempel werden in `data/logs/form-rate-limits.json` gespeichert; abgelaufene Zeitfenster werden beim nächsten gültigen Versuch bereinigt. Die Roh-IP wird vom Rate-Limiter nicht gespeichert.

Kontakt-, Feedback- und Kalender-Anfragen verwenden CSRF-Token, serverseitige Validierung, Datenschutz-Zustimmung und ein Honeypot-Feld. Kontaktanfragen werden lokal in `data/messages/contact.log`, Website-Feedback in `data/messages/feedback.log` protokolliert. Feedbackname und Rueckmailadresse sind optional; der Kommentar wird an `info@marcusreiser.de` weitergeleitet. Bei gesetztem `RESEND_API_KEY` erfolgt der Versand ueber die Resend-API, andernfalls wird `mail()` als Server-Fallback verwendet. Fuer Resend muessen `RESEND_API_KEY` und eine verifizierte `RESEND_FROM_EMAIL` in `.env` gesetzt sein. Den API-Schluessel niemals committen oder weitergeben.

Die Kalenderseite bietet auch bei deaktivierter Bestellfunktion eine Blattvorschau und Bildauswahl. Bestellanfragen sind standardmaessig ausgeschaltet: `CALENDAR_ORDERS_ENABLED=false` blendet Formular und Bestellbutton aus und weist auch den POST-Endpunkt ab. Zum spaeteren Reaktivieren in der Server-`.env` den Wert auf `true` setzen; Formular, Validierung und Speicherung in `data/messages/orders.log` bleiben im Code erhalten. Die Motivauswahl wird im Browser gespeichert und erst mit aktivierter Bestellanfrage uebermittelt. Bestellungen sind Anfragen, keine bezahlten Online-Kaeufe; Zahlung, Rechnungserstellung und automatische Uebergabe an eine Druckerei sind nicht integriert. Historische Anfragen bleiben gespeichert und in der Statistik sichtbar. Kontakt-, Feedback- und Kalenderanfragen werden nicht automatisch geloescht; fuer Auskunft oder Loeschung an `info@marcusreiser.de` wenden.

Der Besucherzaehler speichert Gesamt-, Pfad-, Tages- und Session-Tageswerte in `data/logs/visits.json`. Die interne Statistik zeigt Besucherzahlen fuer heute, eindeutige Besuche heute, den aktuellen Monat, insgesamt, den Verlauf der letzten 14 Tage und gespeicherte Kalenderanfragen.

## Konfiguration und Geheimnisse

`.env.example` enthaelt nur Beispielwerte. Kopiere sie lokal zu `.env` und setze eigene Werte. `.env` ist in `.gitignore` ausgeschlossen und darf nicht in Git eingecheckt oder zusammen mit Website-Dateien hochgeladen werden.

| Variable | Zweck |
| --- | --- |
| `APP_ENV` | Beispielwert in `.env.example`; wird vom aktuellen Anwendungscode nicht ausgewertet |
| `APP_NAME` | Beispielwert in `.env.example`; wird vom aktuellen Anwendungscode nicht ausgewertet |
| `STATS_PASSWORD` | Passwort fuer den internen Statistikbereich; nicht fuer den WCP-Verzeichnisschutz verwenden |
| `CALENDAR_ORDERS_ENABLED` | Kalenderbestellungen aktivieren; Standard `false`, fuer die Reaktivierung auf `true` setzen |
| `PHOTO_LIBRARY_PATH` | Optionaler absoluter Pfad zur Fotoquelle; auf netcup leer lassen, um `data/photos/` zu verwenden |
| `RESEND_API_KEY` | Geheim gehaltener API-Schluessel fuer den Versand von Kontaktanfragen ueber Resend |
| `RESEND_FROM_EMAIL` | Absenderadresse auf einer bei Resend verifizierten Domain, z. B. `info@marcusreiser.de` |

Fuer den Webspace bei Bedarf eine eigene `.env` in `httpdocs/` ueber den Dateimanager anlegen. Dort ein neues, starkes `STATS_PASSWORD` setzen. Die lokale `.env` bleibt ausserhalb von Git und wird nicht hochgeladen. Der Passwortschutz der Testseite wird im netcup WCP verwaltet, nicht ueber `STATS_PASSWORD`.

## Deployment bei netcup

Die Website ist unter `https://marcusreiser.de/` live. Der Dokumentenstamm ist `/marcusreiser.de/httpdocs`; vorhandene weitere Webroot-Ordner bleiben erhalten. Das Plesk-Git-Repository `homepage.git` verfolgt den GitHub-Branch `main` und stellt nach `httpdocs/` bereit.

1. Aenderungen am Code gezielt stagen, committen und zu GitHub pushen. Keine `.env`, privaten Bilder oder lokalen Anfragedateien committen.
2. In Plesk unter **Websites & Domains → marcusreiser.de → Git** zuerst **Jetzt Pull ausfuehren**, danach **Jetzt bereitstellen** waehlen. Das Repository-Ziel ist `/marcusreiser.de/httpdocs`.
3. Den Passwortschutz fuer die oeffentliche Website nicht wieder aktivieren. Geheimnisse und Server-Zugangsdaten gehoeren nicht in Git.
4. Oeffentliche Fotos per FTP ausschliesslich nach `httpdocs/data/photos/20.02_Web/<Kategorie>/` laden. Der FTP-Benutzer `foto-upload` ist auf `httpdocs/data/photos/` beschraenkt. Andere Archiv-Hauptordner werden vom Website-Bildendpunkt abgewiesen.
5. Wasserzeichen-WebPs liegen in `httpdocs/data/photo-cache/`. Bei aktivem GD erzeugt der Bild-Endpunkt fehlende oder veraltete Varianten beim Abruf. Ohne GD zuerst lokal ausschliesslich `20.02_Web` mit `tools/generate_photo_variants.py --only-web-export` verarbeiten und den Cache hochladen.
6. Eine Server-`.env` mit eigenen Werten fuer Mailversand und Statistikpasswort anlegen. Lokale `.env`, `.git/`, `zugangslink.txt`, private Bilder und Anfragedateien nicht hochladen.
7. Pruefen, dass `data/logs/` und `data/messages/` durch PHP beschreibbar sind. Keine pauschalen `777`-Rechte vergeben.
8. Die Seite in einem privaten Browserfenster testen: Startseite, festes Hero-Bild, alle `20.02_Web`-Ordner einschliesslich leerer Ordner, Galerie-Bildabruf mit Wasserzeichen, Kalender-Motivauswahl, Favicon, Feedbackdialog, Kontaktformular und Impressum. Sicherstellen, dass `/kalender-bestellung` bei `CALENDAR_ORDERS_ENABLED=false` nicht annimmt und Bildanfragen ausserhalb `20.02_Web` 404 liefern.

Wenn Kategorien erscheinen, Bilder aber fehlen, zuerst Dateipfade und Gross-/Kleinschreibung der Originale, danach die WebP-Dateien in `data/photo-cache/preview/` und `data/photo-cache/gallery/` pruefen. Bei ersetzten Bildern muss der PHP-Cache-Fix aus `public/photo.php` live bereitgestellt sein.

## Vor der Veroeffentlichung

- Impressum und Datenschutzerklaerung mit den endgueltigen Angaben vervollstaendigen.
- Alle Galerie-, Unterordner-, Kontakt- und Bestellwege testen.
- Sicherstellen, dass keine privaten Aufnahmen im Fotoverzeichnis liegen.
- WCP-Testschutz entfernen oder passend anpassen, wenn die Website oeffentlich gehen soll.
- In netcup-Logs nach PHP-Fehlern sehen und Schreibrechte fuer Datenordner pruefen.
