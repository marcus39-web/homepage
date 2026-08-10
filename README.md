# marcusreiser.de

Persoenliche Homepage von Marcus Reiser mit Fokus auf Fotografie und IT.

## Ueberblick

Das Projekt nutzt einen schlanken PHP-Frontcontroller mit wiederverwendbaren Layout-Komponenten.
Alle Seiten werden zentral ueber `index.php` geroutet und ueber Dateien in `Components/pages` gerendert.

## Technologie

- PHP 8+
- Klassisches CSS (ohne Build-Tool)
- Apache-Rewrite-Regeln fuer Clean URLs im Live-Betrieb

## Projektstruktur

```text
Components/
	images/             # Quellbilder
	layout/             # Header, Navigation, Footer
	pages/              # Seiteninhalte (home, galerie, kontakt, ...)
data/
	certificates.php    # Zertifikatsdaten
	profile.php         # Profildaten
	projects.php        # Projektdaten
public/
	assets/images/      # Oeffentlich ausgelieferte Bilder
	css/style.css       # Zentrales Styling
src/
	Router.php          # Router-Helfer
	View.php            # View-Helfer
tools/
	generate_access_link.php
bootstrap.php         # Initialisierung + Form-Helfer
index.php             # Frontcontroller
.htaccess             # Rewrite/Clean-URL-Regeln fuer Apache
```

## Lokale Entwicklung

### Voraussetzungen

- Installiertes PHP (empfohlen 8.1+)

### Starten

```powershell
php -S 127.0.0.1:8000 -t .
```

Danach im Browser aufrufen:

- http://127.0.0.1:8000/

### Wichtiger Hinweis zu Clean URLs lokal

Der eingebaute PHP-Server (`php -S`) wertet `.htaccess` nicht aus.
Die lokalen Aufrufe funktionieren trotzdem, weil das Routing ueber den Frontcontroller in `index.php` erfolgt.

## Routing

- `GET /` -> Startseite
- `GET /galerie` -> Galerie-Seite
- `GET /kalender` -> Kalender-Seite
- `GET /it-projekte` -> IT-Projekte-Seite
- `GET /contact` -> Kontaktformular
- `POST /contact` -> Verarbeitung des Kontaktformulars
- `GET /impressum` -> Impressum
- `GET /datenschutz` -> Datenschutz

## Kontaktformular

Das Formular ist in `Components/pages/contact.php` eingebunden.
Die Verarbeitung erfolgt zentral in `bootstrap.php`.

Umgesetzte Schutzmechanismen:

- CSRF-Token
- Honeypot-Feld
- Serverseitige Validierung
- Flash-Messages fuer Erfolg/Fehler
- Speicherung der Nachrichten in `data/messages/contact.log`

## Konfiguration

- `.env.example` enthaelt Beispielwerte
- `.env` ist fuer lokale Werte vorgesehen und per `.gitignore` ausgeschlossen

## Deployment-Hinweise

- Zielserver sollte Apache mit `mod_rewrite` nutzen
- `DocumentRoot` auf das Projektverzeichnis setzen
- Schreibrechte fuer `data/messages/` sicherstellen
- In Produktion `APP_ENV=production` setzen

## Inhaltliche Schwerpunkte der Startseite

- Hero mit Hintergrundbild und Profilbild
- Intro-Abschnitt
- Fotografie-Vorschau
- Kalender-Teaser
- IT-Projekte-Teaser
- Footer mit Kontakt- und Rechtliches-Links
