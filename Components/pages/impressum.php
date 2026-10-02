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
    <h2>Angaben gemäß § 5 DDG</h2>
    <address>
      Marcus Reiser<br>
      Lerchenweg 16<br>
      99428 Weimar-Legefeld
    </address>

    <h2>Kontakt</h2>
    <p>E-Mail: <a href="mailto:info@marcusreiser.de">info@marcusreiser.de</a></p>

    <h2>Verantwortlich für den Inhalt</h2>
    <p>Marcus Reiser, Anschrift wie oben.</p>

    <a class="btn btn-primary" href="/">Zur Startseite</a>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
