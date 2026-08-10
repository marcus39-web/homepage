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
- `POST /kalender-bestellung` -> Verarbeitung Kalender-Bestellung
- `GET /it-projekte` -> IT-Projekte-Seite
- `GET /statistik-login` -> Login fuer interne Statistik
- `POST /statistik-login` -> Login-Verarbeitung
- `GET /statistik-logout` -> Logout aus Statistikbereich
- `GET /statistik` -> Interne Statistik (passwortgeschuetzt)
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

## Besucherzaehler

Der Besucherzaehler wird serverseitig bei erfolgreichen `GET`-Aufrufen aktualisiert.
Gespeichert werden:

- Gesamtbesuche
- Besuche pro Pfad
- Tagesbesuche
- Eindeutige Tagesbesuche je Session

Datei:

- `data/logs/visits.json`

Auf der Startseite wird im Hero dezent `Besucher Gesamt` angezeigt.

## Kalender-Bestellungen

Die Kalenderseite enthaelt ein eigenes Bestellformular mit CSRF-, Honeypot- und Pflichtfeldpruefung.
Bestellungen werden als JSON-Zeilen gespeichert in:

- `data/messages/orders.log`

In der internen Statistik kannst du sehen, wie viele Bestellungen eingegangen sind und von wem (Name/E-Mail/Menge/Nachricht).

## Interne Statistik (passwortgeschuetzt)

Die Seite `/statistik` ist nur nach Login erreichbar.
Das Passwort wird nicht im Code hinterlegt, sondern ueber ENV gesetzt:

- `STATS_PASSWORD` in `.env`

Ablauf:

- Nicht eingeloggte Nutzer werden auf `/statistik-login` umgeleitet
- Erfolgreicher Login setzt eine Session-Authentifizierung
- Logout erfolgt ueber `/statistik-logout`

## Konfiguration

- `.env.example` enthaelt Beispielwerte
- `.env` ist fuer lokale Werte vorgesehen und per `.gitignore` ausgeschlossen
- `STATS_PASSWORD` steuert den Zugriff auf die interne Statistik

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
