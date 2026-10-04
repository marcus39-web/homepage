<?php declare(strict_types=1);

$pageTitle = "Datenschutz – Marcus Reiser";
$pageDescription = "Datenschutzerklärung gemäß DSGVO.";
$navContext = "subpage";
$bodyClass = "subpage";
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="subpage-top">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
    <div class="wrap subpage-head">
        <p class="eyebrow-lite">Datenschutz</p>
        <h1>Datenschutzerklärung</h1>
    </div>
</div>

<div class="subpage-main wrap">

    <section class="panel">
        <h2>Allgemeine Hinweise</h2>
        <p>
            Der Schutz deiner persönlichen Daten ist mir wichtig. Personenbezogene Daten werden vertraulich
            und gemäß den gesetzlichen Datenschutzvorschriften behandelt.
        </p>
    </section>

    <section class="panel">
        <h2>Verantwortliche Stelle</h2>
        <p>
            Marcus Klaus‑Dieter Reiser<br>
            Fotografie & IT · Weimar
        </p>
    </section>

    <section class="panel">
        <h2>Hosting und Besucherstatistik</h2>
        <p>
            Beim Aufruf der Website verarbeitet der Hostinganbieter netcup technische Verbindungsdaten,
            insbesondere IP-Adresse, Zeitpunkt, angeforderte Adresse, Browserinformationen und Status der Anfrage,
            um die Website auszuliefern und vor Missbrauch zu schützen. Die Verarbeitung durch netcup richtet sich
            auch nach dessen Datenschutzhinweisen und den dort genannten Speicherfristen.
        </p>
        <p>
            Die interne Besucherstatistik speichert Gesamtaufrufe, aufgerufene Seiten, Tageszahlen und eine
            sitzungsbasierte Tageszählung in einer lokalen Statistikdatei. Der eigene Besucherzähler speichert
            dabei keine IP-Adresse. Die Statistikdaten werden derzeit nicht automatisch gelöscht. Daneben kann
            der Hostinganbieter eigene technische Serverprotokolle mit IP-Adresse nach seinen Speicherfristen führen.
        </p>
    </section>

    <section class="panel">
        <h2>Sitzungen und Cookies</h2>
        <p>
            Für Formulare, CSRF-Schutz und die sitzungsbasierte Besucherzählung verwendet die Website eine
            technisch notwendige PHP-Sitzung. Das Sitzungs-Cookie ist auf die Dauer der Browsersitzung begrenzt,
            wird mit den Sicherheitsoptionen HttpOnly und SameSite=Lax gesetzt und bei HTTPS als Secure markiert.
            Es werden keine Werbe- oder Analyse-Cookies eingesetzt.
        </p>
        <p>
            Die Kalenderauswahl, das gewählte Bundesland und die Ferienanzeige werden zusätzlich im localStorage
            des Browsers gespeichert, damit deine Einstellungen beim nächsten Besuch auf demselben Gerät erhalten
            bleiben. Diese Angaben werden erst mit einer abgesendeten Kalenderanfrage an den Server übermittelt.
        </p>
    </section>

    <section class="panel">
        <h2>Fotos und EXIF-Daten</h2>
        <p>
            Bei veröffentlichten Fotos können EXIF-Daten verarbeitet und in der vergrößerten Bildansicht angezeigt
            werden. Dazu gehören, soweit im Foto vorhanden, Aufnahmezeit, Kameramodell, Objektiv, Brennweite,
            Blende, Belichtungszeit, ISO-Wert und GPS-Koordinaten. Der Bild-Endpunkt stellt diese Angaben dem
            Browser auf Anfrage über den HTTP-Header X-Photo-Exif bereit. Die Angaben werden aus der Bilddatei
            gelesen und nicht in einer separaten EXIF-Datenbank gespeichert. Veröffentliche deshalb nur Bilder,
            deren Metadaten und Aufnahmeorte du teilen möchtest.
        </p>
    </section>

    <section class="panel">
        <h2>Open-Meteo und Nominatim</h2>
        <p>
            Wenn ein geöffnetes Foto GPS-Koordinaten enthält, fragt der Browser bei Nominatim den ungefähren Ortsnamen
            ab. Sind zusätzlich Aufnahmedatum und -zeit vorhanden, fragt der Browser bei Open-Meteo historische oder
            aktuelle Wetterdaten für diesen Ort und Zeitpunkt ab. Dabei werden Koordinaten und – bei Open-Meteo –
            das Aufnahmedatum an den jeweiligen Dienst übermittelt. Die Dienste erhalten dabei technisch auch die
            IP-Adresse des anfragenden Browsers. Ohne GPS-Daten werden diese Abfragen nicht ausgeführt.
        </p>
    </section>

    <section class="panel">
        <h2>Kontakt- und Kalenderanfragen</h2>
        <p>
            Bei einer Kontaktanfrage werden Name, E-Mail-Adresse und Nachricht in einer lokalen Protokolldatei
            gespeichert und zur Bearbeitung an info@marcusreiser.de versendet. Ist Resend konfiguriert, wird die
            Nachricht über diesen E-Mail-Dienst übermittelt; andernfalls nutzt der Server seinen Mailversand.
        </p>
        <p>
            Bei einer Kalenderanfrage werden Name, E-Mail-Adresse, Stückzahl, Nachricht, ausgewählte Monatsmotive,
            Zeitpunkt und IP-Adresse in einer lokalen Datei gespeichert. Eine Benachrichtigung mit Name, E-Mail,
            Stückzahl, Nachricht und Monatsmotiven wird über Resend an info@marcusreiser.de gesendet; die IP-Adresse
            wird nicht in diese E-Mail aufgenommen. Es handelt sich derzeit um eine Anfrage, nicht um einen bezahlten
            Online-Kauf; Zahlung und automatische Druckerei-Beauftragung sind nicht integriert.
        </p>
        <p>
            Kontakt- und Kalenderanfragen werden derzeit nicht automatisch gelöscht. Für Auskunft oder Löschung
            kannst du dich an info@marcusreiser.de wenden.
        </p>
    </section>

    <section class="panel">
        <h2>Rechte der Nutzer</h2>
        <ul class="project-list">
            <li>Auskunft über gespeicherte Daten</li>
            <li>Berichtigung fehlerhafter Daten</li>
            <li>Löschung personenbezogener Daten</li>
            <li>Einschränkung der Verarbeitung</li>
            <li>Widerspruch gegen die Verarbeitung</li>
            <li>Beschwerde bei einer Datenschutz-Aufsichtsbehörde</li>
        </ul>
    </section>

</div>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
