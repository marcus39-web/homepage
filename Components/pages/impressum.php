<?php declare(strict_types=1);

$pageTitle = "Impressum – Marcus Reiser";
$pageDescription = "Impressum gemäß § 5 TMG.";
$navContext = "subpage";
$bodyClass = "subpage";
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="subpage-top">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
    <div class="wrap subpage-head">
        <p class="eyebrow-lite">Impressum</p>
        <h1>Impressum</h1>
    </div>
</div>

<main class="subpage-main wrap legal-document">

    <section class="legal-section" id="verantwortlich">
        <h2>1. Verantwortlich</h2>
        <p>
            Marcus Klaus‑Dieter Reiser<br>
            Fotografie & IT<br>
            Lerchenweg 16<br>
            99428 Weimar / Legefeld<br>
            Deutschland
        </p>
    </section>

    <section class="legal-section" id="kontakt">
        <h2>2. Kontakt</h2>
        <p>
            E‑Mail: <a href="mailto:info@marcusreiser.de">info@marcusreiser.de</a><br>
            Website: <a href="https://marcusreiser.de">marcusreiser.de</a>
        </p>
    </section>

    <section class="legal-section" id="haftung-inhalte">
        <h2>3. Haftung für Inhalte</h2>
        <p>
            Die Inhalte dieser Website wurden mit größter Sorgfalt erstellt. Für die Richtigkeit,
            Vollständigkeit und Aktualität der Inhalte kann jedoch keine Gewähr übernommen werden.
        </p>
    </section>

    <section class="legal-section" id="haftung-links">
        <h2>4. Haftung für Links</h2>
        <p>
            Diese Website enthält Links zu externen Webseiten Dritter, auf deren Inhalte ich keinen Einfluss habe.
            Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter verantwortlich.
        </p>
    </section>

    <section class="legal-section" id="urheberrecht">
        <h2>5. Urheberrecht</h2>
        <p>
            Alle Fotografien und Inhalte auf dieser Website sind urheberrechtlich geschützt.
            Jegliche Nutzung, Vervielfältigung oder Weitergabe ist ohne schriftliche Genehmigung untersagt.
        </p>
    </section>

</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
