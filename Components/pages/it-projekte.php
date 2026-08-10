<?php

declare(strict_types=1);

$pageTitle = 'IT-Projekte - Marcus Reiser';
$pageDescription = 'IT-Projekte von Marcus Reiser: Cloud, Workflows, Automationen und Schulungsunterlagen.';
$bodyClass = 'subpage';
$currentPage = 'it-projekte';

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="subpage-top">
  <?php
  $navContext = 'subpage';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>
</header>

<main class="subpage-main wrap">
  <section class="subpage-head panel">
    <p class="eyebrow-lite">Technik und Praxis</p>
    <h1>IT-Projekte</h1>
    <p>Ausgewählte Arbeitsfelder aus Infrastruktur, Plattformbetrieb und digitaler Wissensvermittlung.</p>
    <a class="btn btn-primary" href="/">Zur Startseite</a>
  </section>

  <section class="project-cards" aria-label="IT Projektkategorien">
    <article class="panel project-item">
      <h2>Cloud und Hosting</h2>
      <p>Aufbau und Betrieb von Webprojekten inklusive Domain, Struktur und Deployment für resier.de und marcusreiser.de.</p>
    </article>
    <article class="panel project-item">
      <h2>Windows und iPadOS Workflows</h2>
      <p>Praxisorientierte Workflows für produktives Arbeiten zwischen Desktop, Tablet und mobilen Einsatzszenarien.</p>
    </article>
    <article class="panel project-item">
      <h2>Fotografie-Automationen</h2>
      <p>Automatisierte Bildabläufe von der Sortierung bis zur Ausgabe für Portfolio, Kalender und Social-Media-Kanäle.</p>
    </article>
    <article class="panel project-item">
      <h2>Schulung und Dokumentation</h2>
      <p>Konzeption von Unterlagen und Leitfäden für Erwachsenenbildung, Technik-Einweisung und Wissenssicherung.</p>
    </article>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
