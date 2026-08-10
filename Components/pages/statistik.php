<?php

declare(strict_types=1);

$pageTitle = 'Statistik - Marcus Reiser';
$pageDescription = 'Interne Uebersicht ueber Besuche und Kalender-Bestellungen.';
$bodyClass = 'subpage';
$currentPage = '';

// Kennzahlen werden aus Dateispeicher geladen (visits.json + orders.log).
$stats = get_visit_stats();
$orders = get_calendar_orders();

// Aufbereitung fuer die KPI-Karten in der Uebersicht.
$today = date('Y-m-d');
$todayVisits = (int) (($stats['daily'][$today] ?? 0));
$todayUnique = (int) (($stats['unique_daily'][$today] ?? 0));
$totalVisits = (int) ($stats['total'] ?? 0);
$totalOrders = count($orders);
$lastVisit = is_string($stats['last_visit'] ?? null) ? $stats['last_visit'] : null;

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="subpage-top">
  <?php
  $navContext = 'subpage';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>
</header>

<main class="subpage-main wrap">
  <section class="panel" aria-labelledby="stats-title">
    <p class="eyebrow-lite">Interne Statistik</p>
    <h1 id="stats-title">Besuche und Bestellungen</h1>
    <p>Diese Seite zeigt dir die wichtigsten Kennzahlen deiner Homepage.</p>

    <div class="stats-grid">
      <article class="stats-card">
        <h2>Besuche gesamt</h2>
        <p class="stats-value"><?= e((string) $totalVisits) ?></p>
      </article>
      <article class="stats-card">
        <h2>Besuche heute</h2>
        <p class="stats-value"><?= e((string) $todayVisits) ?></p>
      </article>
      <article class="stats-card">
        <h2>Eindeutige Besuche heute</h2>
        <p class="stats-value"><?= e((string) $todayUnique) ?></p>
      </article>
      <article class="stats-card">
        <h2>Bestellungen gesamt</h2>
        <p class="stats-value"><?= e((string) $totalOrders) ?></p>
      </article>
    </div>

    <p class="stats-meta">
      Letzter Besuch: <?= $lastVisit !== null ? e($lastVisit) : 'Noch keine Daten' ?>
    </p>
  </section>

  <section class="panel" aria-labelledby="orders-title">
    <h2 id="orders-title">Bestellungen von wem</h2>

    <?php if ($orders === []): ?>
      <p>Noch keine Bestellungen vorhanden.</p>
    <?php else: ?>
      <div class="orders-table-wrap">
        <table class="orders-table">
          <thead>
            <tr>
              <th>Zeitpunkt</th>
              <th>Name</th>
              <th>E-Mail</th>
              <th>Menge</th>
              <th>Nachricht</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= e((string) ($order['timestamp'] ?? '')) ?></td>
                <td><?= e((string) ($order['name'] ?? '')) ?></td>
                <td><?= e((string) ($order['email'] ?? '')) ?></td>
                <td><?= e((string) ($order['quantity'] ?? '')) ?></td>
                <td><?= e((string) ($order['message'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
