<?php declare(strict_types=1);

$pageTitle = "Statistik – Marcus Reiser";
$pageDescription = "Besucherstatistik der Website.";
$navContext = "subpage";
$bodyClass = "subpage";

// Besuchsstatistik im selben Schema laden, das der Frontcontroller schreibt.
$stats = get_visit_stats();
$orders = get_calendar_orders();
$calendarMotifCatalog = calendar_motif_catalog();
$daily = $stats['daily'];
$total = $stats['total'];
$today = date("Y-m-d");
$month = date("Y-m");
$todayUnique = (int) ($stats['unique_daily'][$today] ?? 0);

$todayCount = (int) ($daily[$today] ?? 0);

$monthCount = 0;
foreach ($daily as $date => $count) {
    if (str_starts_with((string) $date, $month)) {
        $monthCount += (int) $count;
    }
}
$lastVisit = $stats['last_visit'];
$orderCount = count($orders);

// Verlauf für die letzten 14 Tage
$days = [];
for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
    $date = date("Y-m-d", strtotime("-$daysAgo days"));
    $days[] = ["date" => $date, "count" => (int) ($daily[$date] ?? 0)];
}
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="subpage-top">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
    <div class="wrap subpage-head">
        <p class="eyebrow-lite">Statistik</p>
        <h1>Besucherzahlen</h1>
        <p>Automatisch generierte Statistik basierend auf deinen Logdaten.</p>
    </div>
</div>

<div class="subpage-main wrap">

    <!-- Übersicht -->
    <section class="panel">
        <h2>Übersicht</h2>

        <div class="project-cards">
            <div class="project-item panel">
                <h3>Heute</h3>
                <p><?= $todayCount ?> Besucher</p>
            </div>

            <div class="project-item panel">
                <h3>Diesen Monat</h3>
                <p><?= $monthCount ?> Besucher</p>
            </div>

            <div class="project-item panel">
                <h3>Gesamt</h3>
                <p><?= $total ?> Besucher</p>
            </div>

            <div class="project-item panel">
                <h3>Kalenderanfragen</h3>
                <p><?= $orderCount ?> Anfragen</p>
            </div>

            <div class="project-item panel">
                <h3>Eindeutige Besuche heute</h3>
                <p><?= $todayUnique ?> Besucher</p>
            </div>
        </div>
        <p class="stats-meta">Letzter Besuch: <?= $lastVisit !== null ? e($lastVisit) : 'Noch keine Daten' ?></p>
    </section>

    <!-- Verlauf -->
    <section class="panel" style="margin-top: 2rem;">
        <h2>Verlauf der letzten 14 Tage</h2>

        <table class="stats-table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Besucher</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($days as $d): ?>
                    <tr>
                        <td><?= $d["date"] ?></td>
                        <td><?= $d["count"] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="panel" style="margin-top: 2rem;" aria-labelledby="orders-title">
        <h2 id="orders-title">Kalenderanfragen</h2>

        <?php if ($orders === []): ?>
            <p>Noch keine Anfragen vorhanden.</p>
        <?php else: ?>
            <div class="orders-table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Zeitpunkt</th>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Menge</th>
                            <th>Monatsmotive</th>
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
                                <td>
                                    <?php foreach ((array) ($order['motifs'] ?? []) as $month => $motifId): ?>
                                        <?php if (is_string($motifId) && isset($calendarMotifCatalog[$motifId])): ?>
                                            <div><strong><?= e((string) $month) ?>:</strong> <?= e($calendarMotifCatalog[$motifId]['label']) ?></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                                <td><?= e((string) ($order['message'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
