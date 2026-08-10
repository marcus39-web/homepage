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
    <p>Diese Vorlage bitte mit deiner finalen Datenschutzerklärung ersetzen.</p>
    <p>
      Auf dieser Website werden aktuell keine Tracking-Cookies und keine externen Analyse-Tools eingesetzt.
      Bei Kontaktaufnahme per E-Mail werden die übermittelten Daten nur zur Bearbeitung deiner Anfrage genutzt.
    </p>
    <a class="btn btn-primary" href="/">Zur Startseite</a>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
