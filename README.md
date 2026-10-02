# marcusreiser.de

Persoenliche Fotografie-Website von Marcus Reiser aus Weimar/Legefeld. Die Website praesentiert eine automatisch gepflegte Fotogalerie und bereitet Angebote fuer Fototassen und einen eigenen Fotokalender vor.

## Aufbau

Die Anwendung ist eine klassische PHP-Website ohne Build-Schritt. `index.php` ist der Frontcontroller: Er behandelt POST-Aktionen, prueft geschuetzte Statistikseiten, zaehlt Seitenaufrufe und laedt das passende Template aus `Components/pages/`. Gemeinsamer HTML-Rahmen und Navigation liegen in `Components/layout/`; das zentrale Styling ist `public/css/style.css`.

| Pfad | Zweck |
| --- | --- |
| `index.php` | Frontcontroller und URL-Routing |
| `bootstrap.php` | Sitzungen, ENV-Konfiguration und gemeinsame Helfer |
| `Components/pages/` | Startseite, Galerie, Kalender, Kontakt und Rechtliches |
| `Components/layout/` | Header, Navigation und Footer |
| `src/PhotoLibrary.php` | Fotoquellen, Kategorien, Dateipfade und Ausschlussregeln |
| `public/photo.php` | Sicherer Bild-Endpunkt fuer private Fotoablage |
| `public/css/style.css` | Layout und responsive Gestaltung |
| `public/assets/images/` | Oeffentliche Web-Assets, darunter das Logo |
| `data/photos/` | Optionale Fotoquelle auf dem Webspace |
| `data/logs/`, `data/messages/` | Besuchsstatistik, Kontaktanfragen und Bestellungen |
| `.htaccess` | Clean URLs sowie Sperre interner Dateien/Verzeichnisse |

## Voraussetzungen

- PHP 8.1 oder neuer
- Apache mit `mod_rewrite` fuer den Produktivbetrieb
- Schreibrechte fuer `data/logs/` und `data/messages/`

## Lokal starten

Im Projektverzeichnis ausfuehren:

```powershell
php -S 127.0.0.1:8000 -t .
```

Anschliessend `http://127.0.0.1:8000/` im Browser oeffnen. Der eingebaute PHP-Server liest `.htaccess` nicht, statische Dateien und Foto-Routen werden deshalb zusaetzlich in `index.php` behandelt.

## URLs und Funktionen

| Methode und URL | Funktion |
| --- | --- |
| `GET /` | Startseite und Ordner-Vorschauen |
| `GET /galerie` | Alle Kategorien und deren Unterordner |
| `GET /galerie?ordner=07_Blumen` | Nur die gewaehlte Kategorie |
| `GET /galerie?ordner=05_Weimar_und_Umgebung&unterordner=Tiefurt` | Einen Unterordner anzeigen; verschachtelte Pfade werden ebenfalls unterstuetzt |
| `GET /public/photo.php?category=...&file=...` | Bildauslieferung nach Pfad- und Dateityppruefung |
| `GET /kalender` | Kalenderinformationen und Bestellformular |
| `POST /kalender-bestellung` | Kalenderbestellung speichern |
| `GET /contact`, `POST /contact` | Kontaktformular und Verarbeitung |
| `GET /statistik-login`, `POST /statistik-login` | Anmeldung zum Statistikbereich |
| `GET /statistik` | Passwortgeschuetzte Besucher- und Bestellstatistik |
| `GET /statistik-logout` | Statistik abmelden |
| `GET /impressum`, `GET /datenschutz` | Rechtliche Informationsseiten |

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

Ordner oder Unterordner, deren Name `Privat` enthaelt, werden sowohl beim Scannen als auch beim Bildabruf ausgeschlossen. Reine Web-Ordner mit nummeriertem Namen und `Web` am Ende, zum Beispiel `20.02_Web`, werden nicht als Fotokategorien angezeigt. Lade nur Bilder hoch, die du auf der Website veroeffentlichen darfst; insbesondere keine RAW-Dateien, Zeugnisse, Passbilder oder privaten Aufnahmen.

`data/` wird durch `.htaccess` gegen direkten HTTP-Zugriff gesperrt. Bilder aus `data/photos/` werden daher ausschliesslich durch `public/photo.php` ausgeliefert. Der Endpunkt erlaubt nur die unterstuetzten Bildtypen und blockiert private Ordner sowie Pfad-Traversal.

## Formulare und gespeicherte Daten

Kontakt- und Kalenderformulare verwenden CSRF-Token, serverseitige Validierung und ein Honeypot-Feld. Kontaktanfragen werden lokal in `data/messages/contact.log` protokolliert; Kalenderbestellungen werden zeilenweise als JSON in `data/messages/orders.log` gespeichert. Der Kontakt-Mailversand ueber `mail()` ist best effort; das lokale Log ist die dauerhafte Speicherung.

Der Besucherzaehler speichert Gesamt-, Pfad-, Tages- und Session-Tageswerte in `data/logs/visits.json`. Die Startseite zeigt die Gesamtbesuche. Die interne Statistik zeigt Besuchszahlen und Kalenderbestellungen.

## Konfiguration und Geheimnisse

`.env.example` enthaelt nur Beispielwerte. Kopiere sie lokal zu `.env` und setze eigene Werte. `.env` ist in `.gitignore` ausgeschlossen und darf nicht in Git eingecheckt oder zusammen mit Website-Dateien hochgeladen werden.

| Variable | Zweck |
| --- | --- |
| `APP_ENV` | Laufzeitumgebung, lokal beispielsweise `development`, produktiv `production` |
| `APP_NAME` | Anwendungsname |
| `STATS_PASSWORD` | Passwort fuer den internen Statistikbereich; nicht fuer den WCP-Verzeichnisschutz verwenden |
| `PHOTO_LIBRARY_PATH` | Optionaler absoluter Pfad zur Fotoquelle; auf netcup leer lassen, um `data/photos/` zu verwenden |

Fuer den Webspace bei Bedarf eine eigene `.env` in `R10/` ueber den Dateimanager anlegen. Dort ein neues, starkes `STATS_PASSWORD` setzen. Der separate Passwortschutz fuer die gesamte Testseite wird im netcup WCP verwaltet.

## Auf netcup testen

Die Testdomain ist `fotografie.marcusreiser.de`; das Webroot soll im WCP auf `httpdocs/R10` zeigen. Bei diesem Document Root wird die Website unter `https://fotografie.marcusreiser.de/` geoeffnet, nicht unter `/R10/`.

1. Im WCP den Verzeichnisschutz fuer den Webroot der Testdomain aktivieren und separat einen Benutzer anlegen.
2. Den Inhalt des Projekts nach `httpdocs/R10/` hochladen. `index.php`, `bootstrap.php`, `.htaccess`, `Components/`, `src/`, `public/` und benoetigte Dateien unter `data/` muessen an dieser Ebene liegen.
3. `public/css/style.css`, `public/photo.php` und `public/assets/images/marcus-reiser-logo.png` in ihrer Projektstruktur belassen.
4. Ausgewaehlte, oeffentliche Foto-Kategorien nach `R10/data/photos/` hochladen. Das Windows-Laufwerk `D:` ist vom Webserver nicht erreichbar.
5. Eine Server-`.env` mit eigenem Statistikpasswort anlegen; lokale `.env`, `.git/`, `zugangslink.txt` und private Bilder nicht hochladen.
6. Pruefen, dass `data/logs/` und `data/messages/` durch PHP beschreibbar sind. Keine pauschalen `777`-Rechte vergeben.
7. In einem privaten Browserfenster `https://fotografie.marcusreiser.de/` oeffnen, den WCP-Zugang testen und Galerie, Unterordner, Kontaktformular sowie Kalenderseite pruefen.

Wenn Kategorien erscheinen, Bilder aber fehlen, zuerst die PHP-Fehlerprotokolle im WCP sowie die Aktualitaet von `src/PhotoLibrary.php` und `public/photo.php` pruefen. Ein HTTP-500 nach Klick auf einen Unterordner deutet typischerweise auf nicht zusammenpassende Versionen von `Components/pages/galerie.php` und `src/PhotoLibrary.php` hin.

## Vor der Veroeffentlichung

- Impressum und Datenschutzerklaerung mit den endgueltigen Angaben vervollstaendigen.
- Alle Galerie-, Unterordner-, Kontakt- und Bestellwege testen.
- Sicherstellen, dass keine privaten Aufnahmen im Fotoverzeichnis liegen.
- WCP-Testschutz entfernen oder passend anpassen, wenn die Website oeffentlich gehen soll.
- In netcup-Logs nach PHP-Fehlern sehen und Schreibrechte fuer Datenordner pruefen.
