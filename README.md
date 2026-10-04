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
- PHP-Erweiterung GD zum Erzeugen/Aktualisieren fehlender WebP-Varianten und zum Einbrennen des Wasserzeichens zur Laufzeit; vorhandene Cachedateien funktionieren ohne GD
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
| `GET /galerie` | Oeffentliche Kategorien als Ordnerkarten; Bilder erscheinen nach Auswahl eines Ordners |
| `GET /galerie?ordner=07_Blumen` | Nur die gewaehlte Kategorie |
| `GET /galerie?ordner=05_Weimar_und_Umgebung&unterordner=Tiefurt` | Einen Unterordner anzeigen; verschachtelte Pfade werden ebenfalls unterstuetzt |
| `GET /galerie?ordner=20.02_Web&unterordner=13_Zwiebelmarkt` | Sonderalbum fuer Zwiebelmarkt-Bilder aus dem Web-Exportordner |
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

Die einzige Web-Ordner-Ausnahme ist das Sonderalbum `20.02_Web/13_Zwiebelmarkt`. Es wird nur ueber die oben gezeigte URL aufgerufen; die uebrigen Inhalte von `20.02_Web` bleiben aus der Galerie ausgeblendet.

`data/` wird durch `.htaccess` gegen direkten HTTP-Zugriff gesperrt. Bilder aus `data/photos/` werden daher ausschliesslich durch `public/photo.php` ausgeliefert. Der Endpunkt erlaubt nur die unterstuetzten Bildtypen und blockiert private Ordner, Passbild-Kategorien sowie Pfad-Traversal.

Bei `HEAD`-Anfragen liefert der Bild-Endpunkt EXIF-Daten im Header `X-Photo-Exif`. Die Lightbox zeigt daraus Aufnahmezeit und GPS-Ort an und fragt fuer GPS-Bilder historische bzw. aktuelle Stundenwerte bei Open-Meteo sowie Ortsnamen bei Nominatim ab. Verwendet wird der Wetterwert zur Aufnahmezeit in `Europe/Berlin`. Dafuer muessen EXIF-Daten vorhanden und externe Anfragen moeglich sein. GPS-Koordinaten oeffentlicher Bilder werden damit auch im HTTP-Header an Besucher ausgeliefert; veroeffentliche nur Fotos, deren Standortdaten du teilen moechtest.

### Optimierte Web-Fotos

Die Druck-Originale bleiben unveraendert. Vor dem Deployment erzeugt Pillow WebP-Dateien: Vorschauen haben maximal 800 Pixel Kantenlaenge bei Qualitaet 78, Galerievarianten maximal 1800 Pixel bei Qualitaet 82.

```powershell
python -m pip install Pillow
python tools/generate_photo_variants.py --source "D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG"
```

Fuer den Kalender-Webexport ausschliesslich aus `20.02_Web`:

```powershell
python tools/generate_photo_variants.py --source "D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG" --only-web-export
```

Die Varianten landen unter `data/photo-cache/preview/` und `data/photo-cache/gallery/`. Der Bild-Endpunkt erzeugt fehlende oder veraltete Varianten mit GD und versieht Vorschau, Galerie und Miniatur mit `public/assets/watermark/watermark.png`. Neue Bilder und ein geaendertes Wasserzeichen erneuern den Cache beim naechsten Abruf automatisch. Die Druck-Originale bleiben unveraendert. Bild-URLs enthalten Aenderungszeit und Dateigroesse der Quelle, damit Browser nach einem Austausch keine alte Variante aus dem Cache wiederverwenden. Ist GD nicht verfuegbar oder schlaegt die Erzeugung fehl, wird das Original ohne Wasserzeichen ausgeliefert; der Webspace muss GD daher aktiviert haben. Fuer kurze Ladezeiten sollten die erzeugten Cache-Dateien mit deployed werden. Einzelne Bilder lassen sich zum Test mit `--match Marcus_Sonnenblumen_2.JPG` verarbeiten.

Die dynamische Kalenderauswahl liest ausschliesslich Bilder aus `data/photos/20.02_Web/` und darin nur aus `01_Gebäude`, `02_Landschaft`, `05_Weimar_und_Umgebung`, `07_Blumen`, `08_Tiere`, `11_Weihnachten`, `12_Kerzen` und `13_Zwiebelmarkt`. Wenn der Webspace WebP-Varianten nicht selbst erzeugt, lassen sich die Cachedateien fuer genau diese Auswahl lokal erstellen:

```powershell
python tools/generate_photo_variants.py --source "D:\10_Fotoarchiv\Canon_R10_Bilder\01_Bibiothek_JPG" --only-web-export
```

Der Projektabschnitt `Immengoldkerzen` auf der Startseite verwendet `data/photos/20.02_Web/12_Kerzen_Immengold_Isabelle_Krämer/Kerzen_Vielfalt.jpg` als Kachelbild. Die Kachel verlinkt auf die Schwesterseite `https://immengold.com`.

Das Wasserzeichen erschwert eine unveraenderte Weiterverwendung, verhindert aber keine Screenshots oder das Speichern eines im Browser angezeigten Bildes.

Die Kalenderauswahl verwendet zusaetzlich vorbereitete WebP-Dateien unter `public/assets/images/galerie/natur/Ilm/`. Die JPG-Originale fuer den Druck bleiben davon unberuehrt.

## Formulare und gespeicherte Daten

Kontakt- und Kalender-Anfrageformulare verwenden CSRF-Token, serverseitige Validierung, Datenschutz-Zustimmung und ein Honeypot-Feld. Kontaktanfragen werden lokal in `data/messages/contact.log` protokolliert und bei gesetztem `RESEND_API_KEY` ueber die Resend-API versendet; ohne API-Schluessel verwendet das Kontaktformular `mail()` als Server-Fallback. Fuer Resend muessen `RESEND_API_KEY` und eine verifizierte `RESEND_FROM_EMAIL` in `.env` gesetzt sein. Den API-Schluessel niemals committen oder weitergeben. Die Kalenderseite bietet eine Blattvorschau und eine Bildauswahl fuer jeden Monat. Die Auswahl wird im Browser gespeichert und mit der Anfrage uebermittelt. Kalenderanfragen werden vor dem Mailversand zeilenweise als JSON in `data/messages/orders.log` gespeichert; eine Benachrichtigung mit den gewaehlten Motiven geht an `info@marcusreiser.de`. Dies ist eine Anfrage, kein bezahlter Online-Kauf: Zahlung, Rechnungserstellung und automatische Uebergabe an eine Druckerei sind noch nicht integriert. Scheitert der Mailversand, bleibt die Anfrage gespeichert und die Rueckmeldung weist darauf hin. Die Statistikseite zeigt Besucherzahlen sowie gespeicherte Anfragen inklusive Monatsmotiven; sensible IP-Daten werden in der Tabelle nicht angezeigt.

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

Fuer den Webspace bei Bedarf eine eigene `.env` in `httpdocs/` ueber den Dateimanager anlegen. Dort ein neues, starkes `STATS_PASSWORD` setzen. Die lokale `.env` bleibt ausserhalb von Git und wird nicht hochgeladen. Der Passwortschutz der Testseite wird im netcup WCP verwaltet, nicht ueber `STATS_PASSWORD`.

## Deployment bei netcup

Die Website laeuft als geschuetzter Test auf `https://marcusreiser.de/`. Der Dokumentenstamm ist `/marcusreiser.de/httpdocs`; die Bewerbungsordner im Webroot bleiben erhalten. Das Plesk-Git-Repository `homepage.git` verfolgt den GitHub-Branch `main` und stellt nach `httpdocs/` bereit.

1. Aenderungen am Code gezielt stagen, committen und zu GitHub pushen. Keine `.env`, privaten Bilder oder lokalen Anfragedateien committen.
2. In Plesk unter **Websites & Domains → marcusreiser.de → Git** zuerst **Jetzt Pull ausfuehren**, danach **Jetzt bereitstellen** waehlen. Das Repository-Ziel ist `/marcusreiser.de/httpdocs`.
3. Der Verzeichnisschutz fuer `httpdocs` wird separat im netcup WCP verwaltet. Zugangsdaten nicht in Git ablegen.
4. Oeffentliche Fotokategorien separat per FTP in `httpdocs/data/photos/` laden. Der FTP-Benutzer `foto-upload` ist auf diesen Fotoordner beschraenkt. Normale Kategorien liegen direkt in `photos/`; das Zwiebelmarkt-Sonderalbum liegt in `photos/20.02_Web/13_Zwiebelmarkt/`.
5. Die WebP-Varianten liegen in `httpdocs/data/photo-cache/`; sie koennen vorab mit `tools/generate_photo_variants.py` erzeugt werden. Cache-Dateien nach einem JPG-Austausch werden vom Bild-Endpunkt anhand des Quell-Zeitstempels erneuert.
6. Eine Server-`.env` mit eigenen Werten fuer Mailversand und Statistikpasswort anlegen. Lokale `.env`, `.git/`, `zugangslink.txt`, private Bilder und Anfragedateien nicht hochladen.
7. Pruefen, dass `data/logs/` und `data/messages/` durch PHP beschreibbar sind. Keine pauschalen `777`-Rechte vergeben.
8. Die geschuetzte Seite in einem privaten Browserfenster testen: Startseite, Immengoldkerzen-Kachel und Link, Galerie, Foto-Unterordner, Kalender, Kontaktformular und Impressum.

Wenn Kategorien erscheinen, Bilder aber fehlen, zuerst Dateipfade und Gross-/Kleinschreibung der Originale, danach die WebP-Dateien in `data/photo-cache/preview/` und `data/photo-cache/gallery/` pruefen. Bei ersetzten Bildern muss der PHP-Cache-Fix aus `public/photo.php` live bereitgestellt sein.

## Vor der Veroeffentlichung

- Impressum und Datenschutzerklaerung mit den endgueltigen Angaben vervollstaendigen.
- Alle Galerie-, Unterordner-, Kontakt- und Bestellwege testen.
- Sicherstellen, dass keine privaten Aufnahmen im Fotoverzeichnis liegen.
- WCP-Testschutz entfernen oder passend anpassen, wenn die Website oeffentlich gehen soll.
- In netcup-Logs nach PHP-Fehlern sehen und Schreibrechte fuer Datenordner pruefen.
