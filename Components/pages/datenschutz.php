<?php

declare(strict_types=1);

$pageTitle = 'Datenschutz - Marcus Reiser';
$pageDescription = 'Datenschutzhinweise von marcusreiser.de.';
$bodyClass = 'subpage';
$currentPage = 'datenschutz';

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="subpage-top">
  <?php
  $navContext = 'subpage';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>
</header>

<main class="subpage-main wrap">
  <section class="panel legal-block">
    <h1>Datenschutz</h1>
    <p>Diese Datenschutzhinweise informieren darüber, welche personenbezogenen Daten beim Besuch dieser Website und bei der Nutzung ihrer Formulare verarbeitet werden.</p>

    <h2>Verantwortlicher</h2>
    <p>
      Marcus Reiser<br>
      Lerchenweg 16<br>
      99428 Weimar-Legefeld<br>
      E-Mail: <a href="mailto:info@marcusreiser.de">info@marcusreiser.de</a>
    </p>

    <h2>Hosting und Server-Protokolle</h2>
    <p>Diese Website wird bei der netcup GmbH, Emmy-Noether-Straße 10, D-76131 Karlsruhe, gehostet. Beim Aufruf der Website verarbeitet der Hosting-Anbieter technisch erforderliche Verbindungsdaten. Dazu können insbesondere IP-Adresse, Zeitpunkt, angeforderte Seite, Referrer, Browser- und Geräteinformationen sowie HTTP-Status gehören. Die Verarbeitung dient der Auslieferung, Stabilität und Sicherheit der Website.</p>
    <p><strong>[Speicherdauer der Server-Protokolle beim Hosting-Anbieter ergänzen.]</strong> Rechtsgrundlage ist das berechtigte Interesse am sicheren und funktionsfähigen Betrieb der Website (Art. 6 Abs. 1 lit. f DSGVO).</p>

    <h2>Session-Cookie und Besuchszählung</h2>
    <p>Die Website verwendet ein PHP-Session-Cookie, damit Formulare und sicherheitsrelevante Funktionen wie CSRF-Schutz funktionieren. Das Cookie ist technisch erforderlich und wird grundsätzlich beim Schließen des Browsers gelöscht. Es wird kein Werbe-Cookie gesetzt.</p>
    <p>Für eine interne Besuchsstatistik werden Gesamtaufrufe, aufgerufene Seiten und Tageswerte gespeichert. Eine tägliche Wiedererkennung erfolgt über die Session. Die Statistikdatei enthält nach der aktuellen Implementierung keine IP-Adresse und kein geräteübergreifendes Nutzerprofil. Rechtsgrundlage ist das berechtigte Interesse an einer aggregierten Auswertung und am Betrieb der Website (Art. 6 Abs. 1 lit. f DSGVO).</p>

    <h2>Kontaktformular</h2>
    <p>Wenn du das Kontaktformular nutzt, werden Name, E-Mail-Adresse und Nachricht verarbeitet. Die Angaben werden zur Bearbeitung der Anfrage in einer Serverdatei gespeichert und per E-Mail an Marcus Reiser übermittelt. Die Website löscht Einträge in der Serverdatei nicht automatisch. <strong>[Aufbewahrungs- und Löschfrist für Serverdatei und E-Mail-Postfach festlegen und umsetzen.]</strong></p>
    <p>Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO, sofern die Anfrage auf einen Vertrag oder vorvertragliche Maßnahmen gerichtet ist; andernfalls Art. 6 Abs. 1 lit. f DSGVO (Bearbeitung und Beantwortung der Anfrage).</p>

    <h2>Kalenderanfragen</h2>
    <p>Bei einer Kalenderanfrage werden Name, E-Mail-Adresse, Stückzahl, eine freiwillige Nachricht, Zeitpunkt und die IP-Adresse verarbeitet und in einer Serverdatei gespeichert. Die Angaben dienen der Bearbeitung der Anfrage und der Abstimmung einer möglichen Bestellung. Die Website löscht diese Einträge nicht automatisch. <strong>[Aufbewahrungs- und Löschfrist für Bestellanfragen festlegen und umsetzen; gesetzliche Aufbewahrungspflichten berücksichtigen.]</strong></p>
    <p>Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO für vorvertragliche Maßnahmen und gegebenenfalls die Vertragsabwicklung.</p>

    <h2>Externe Google Fonts</h2>
    <p>Diese Website lädt Schriftarten von Google Fonts. Beim Laden der Seite baut dein Browser dafür eine Verbindung zu Servern von Google auf. Dabei werden insbesondere deine IP-Adresse und technische Verbindungsdaten an Google übermittelt. Anbieter ist Google Ireland Limited beziehungsweise Google LLC; eine Verarbeitung außerhalb des Europäischen Wirtschaftsraums kann nicht ausgeschlossen werden. Weitere Informationen findest du in den <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Datenschutzhinweisen von Google</a>.</p>

    <h2>Empfänger</h2>
    <p>Zugriff auf die Daten haben der Hosting-Anbieter sowie, soweit für die jeweilige Funktion erforderlich, der E-Mail-Dienstleister und Google Fonts. Eine darüber hinausgehende Weitergabe erfolgt nicht, sofern keine gesetzliche Pflicht besteht.</p>

    <h2>Deine Rechte</h2>
    <p>Du hast im Rahmen der gesetzlichen Voraussetzungen das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit und Widerspruch gegen Verarbeitungen auf Grundlage berechtigter Interessen. Eine erteilte Einwilligung kannst du mit Wirkung für die Zukunft widerrufen. Außerdem kannst du dich bei einer Datenschutz-Aufsichtsbehörde beschweren.</p>
    <p>Für Anliegen zum Datenschutz erreichst du Marcus Reiser unter <a href="mailto:info@marcusreiser.de">info@marcusreiser.de</a>.</p>

    <p><strong>Offen:</strong> Speicherdauer der Server-Protokolle sowie Aufbewahrungs- und Löschfristen für Kontakt- und Kalenderanfragen ergänzen und praktisch umsetzen. Die Angaben und Rechtsgrundlagen vor Veröffentlichung prüfen lassen.</p>
    <a class="btn btn-primary" href="/">Zur Startseite</a>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
