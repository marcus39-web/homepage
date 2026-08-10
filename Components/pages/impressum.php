<?php

declare(strict_types=1);

$pageTitle = 'Impressum - Marcus Reiser';
$pageDescription = 'Impressum von marcusreiser.de.';
$bodyClass = 'subpage';
$currentPage = 'impressum';

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
    <h1>Impressum</h1>
    <p>Diese Vorlage bitte mit deinen verbindlichen Impressumsdaten ergänzen.</p>
    <p>
      Marcus Reiser<br>
      Weimar / Legefeld<br>
      E-Mail: info@marcusreiser.de
    </p>
    <a class="btn btn-primary" href="/">Zur Startseite</a>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
