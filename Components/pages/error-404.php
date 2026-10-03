<?php declare(strict_types=1);

$pageTitle = "Seite nicht gefunden – Marcus Reiser";
$pageDescription = "Die angeforderte Seite wurde nicht gefunden.";
$navContext = "subpage";
$bodyClass = "subpage";
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="subpage-top">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
    <div class="wrap subpage-head">
        <p class="eyebrow-lite">Fehler 404</p>
        <h1>Seite nicht gefunden</h1>
        <p>Die angeforderte Seite existiert nicht oder wurde verschoben.</p>
    </div>
</div>

<div class="subpage-main wrap">
    <section class="panel">
        <h2>Was jetzt?</h2>
        <p>
            Du kannst zur Startseite zurückkehren oder die Galerie ansehen.
        </p>

        <div class="hero-actions">
            <a href="/" class="btn btn-primary">Zur Startseite</a>
            <a href="/galerie" class="btn btn-secondary">Zur Galerie</a>
        </div>
    </section>
</div>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
