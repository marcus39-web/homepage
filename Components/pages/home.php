<?php declare(strict_types=1);

$pageTitle = "Marcus Reiser – Fotografie & IT";
$pageDescription = "Fotografie, Kalender, Projekte und IT – die persönliche Website von Marcus Reiser aus Weimar.";
$navContext = "hero";
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="hero">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>

    <!-- Hero-Bild -->
    <div class="hero-media">
        <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('07_Blumen', 'Sonnenblumen/Legefeld/10_09_2026/Marcus_Sonnenblume_2.JPG'), 'gallery')) ?>" alt="Marcus zwischen Sonnenblumen">
    </div>

    <div class="hero-content">
        <div class="hero-logo">
            <img src="/public/assets/images/marcus-reiser-logo.png" alt="Marcus Reiser Fotografie">
        </div>
        <p class="hero-kicker">Fotografie · Kalender · Projekte</p>

        <h1>Willkommen im schönen Weimar</h1>

        <p class="hero-subline">
            Ich bin Marcus Reiser aus Weimar – Fotograf, IT‑Spezialist und kreativer Kopf.
            Hier findest du meine Fotogalerien, meinen Jahreskalender und ausgewählte Projekte.
        </p>

        <div class="hero-actions">
            <a href="/galerie" class="btn btn-primary">Zur Galerie</a>
            <a href="/kalender" class="btn btn-secondary">Kalender ansehen</a>
            <a href="/contact" class="btn btn-ghost">Kontakt aufnehmen</a>
        </div>
    </div>
</div>


<main>

    <!-- Intro-Panel -->
    <section>
        <div class="intro wrap">
            <h1>Fotografie aus Weimar</h1>
            <p>
                Fotografie ist eines meiner Hobbys und ein kreativer Ausgleich zu meiner Arbeit in der IT-Entwicklung.
                Beide Bereiche verbinden für mich Neugier, Ideen und Freude am Ausprobieren: Bei IT-Projekten entstehen digitale Lösungen,
                mit der Kamera halte ich spontane Eindrücke fest. Meine Bilder entstehen unterwegs, bei Veranstaltungen und an besonderen
                Sehenswürdigkeiten – in Weimar und überall dort, wo mir ein Motiv begegnet. Auf dieser Website kommen beide Interessen zusammen:
                Du findest Fotogalerien, Kalenderprojekte sowie Beispiele meiner IT-Arbeit.
            </p>
        </div>
    </section>

    <!-- Zwiebelmarkt-Teaser -->
    <section class="offer-band">
        <div class="wrap">
            <p class="offer-kicker">Weimar · 9. bis 11. Oktober 2026</p>
            <h2>Der 373. Zwiebelmarkt beginnt am 09. Oktober 2026</h2>

            <div class="offer-grid">

                <div class="offer-item">
                    <h3>Weimars große Herbsttradition</h3>
                    <p>Seit 1653 gehört der Zwiebelmarkt zum Herbst in Weimar. Drei Tage lang wird die historische Innenstadt zur Festmeile mit Marktständen, den bekannten Zwiebelzöpfen und Musik auf mehreren Bühnen. Auch der Kinderzwiebelmarkt und der beliebte Stadtlauf gehören zum Programm.</p>
                    <p>Der Auftakt ist am Freitag um 12 Uhr auf dem Markt: Zwiebelmarktkönigin Roswitha I. und Oberbürgermeister Peter Kleine verkosten gemeinsam den traditionellen Zwiebelkuchen.</p>
                </div>

                <div class="offer-item">
                    <h3>Aktuelle Bilder vom Zwiebelmarkt</h3>
                    <p>Ab dem 9. Oktober findest du hier laufend neue Eindrücke vom Marktgeschehen und aus den Gassen der Weimarer Altstadt.</p>
                    <a href="/galerie?<?= e(http_build_query(['ordner' => '20.02_Web', 'unterordner' => '13_Zwiebelmarkt'])) ?>" class="offer-link">Zwiebelmarkt-Bilder ansehen</a>
                    <p><a href="https://www.weimar.de/kultur/veranstaltungen/maerkte-und-feste/zwiebelmarkt/" class="offer-link">Offizielle Informationen der Stadt Weimar</a></p>
                    <p><a href="https://stadt.weimar.de/de/rathauskurier.html" class="offer-link">Rathauskurier 9/2026 (Amtsblatt)</a></p>
                </div>

            </div>
        </div>
    </section>

    <!-- Galerie-Teaser -->
    <section class="wrap">
        <div class="section-head">
            <h2>Galerie</h2>
            <p>Eine Auswahl meiner fotografischen Arbeiten – Natur, Architektur, Tiere und mehr.</p>
        </div>

        <div class="gallery-grid">

            <a href="/galerie?<?= e(http_build_query(['ordner' => '05_Weimar_und_Umgebung', 'unterordner' => 'Weimar_Park'])) ?>" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('05_Weimar_und_Umgebung', 'Weimar_Park/28_08_2026_Park_Allee/Weimarpark_Allee.jpg'), 'preview')) ?>" alt="Weimarpark-Allee">
                <h3>Natur</h3>
            </a>

            <a href="/galerie?<?= e(http_build_query(['ordner' => '05_Weimar_und_Umgebung', 'unterordner' => 'Denkmaeler'])) ?>" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('05_Weimar_und_Umgebung', 'Denkmaeler/Schiller_Goethe_Theater_27_08_2026.JPG'), 'preview')) ?>" alt="Schiller-und-Goethe-Denkmal">
                <h3>Architektur</h3>
            </a>

            <a href="/galerie?<?= e(http_build_query(['ordner' => '08_Tiere', 'unterordner' => 'Voegel'])) ?>" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('08_Tiere', 'Voegel/Rotmilan/Legefeld/12_09_2026/Rotmilan_Legefeld_12_09_2026.JPG'), 'preview')) ?>" alt="Rotmilan im Flug">
                <h3>Tiere</h3>
            </a>

        </div>

        <a href="/galerie" class="calendar-teaser btn btn-primary">Zur kompletten Galerie</a>
    </section>

    <!-- Projekte -->
    <section class="wrap">
        <div class="section-head">
            <h2>Projekte</h2>
            <p>IT‑Projekte, Fotoprojekte und kreative Arbeiten – eine Auswahl meiner aktuellen Tätigkeiten.</p>
        </div>

        <div class="project-cards">

            <div class="project-item panel">
                <h2>Canon R10 Workflow</h2>
                <p>Mein optimierter Workflow für RAW/JPG, Bildauswahl, Varianten und Export.</p>
            </div>

            <div class="project-item panel">
                <h2>Website <span class="project-domain">marcusreiser.de</span></h2>
                <p>Modulares PHP‑System mit Router, Views, automatisierter Galerie und Statistik.</p>
            </div>

            <div class="project-item panel">
                <h2>Kalenderproduktion</h2>
                <p>Jahreskalender mit eigenen Motiven, Druckvorbereitung und Layout.</p>
            </div>

        </div>

        <div class="section-head immengold-section-head">
            <h2>Immengoldkerzen</h2>
            <p>Aus eigener Herstellung meiner Schwester</p>
        </div>

        <div class="project-cards immengold-project-cards">
            <a class="project-item project-item-immengold panel" href="https://immengold.com" target="_blank" rel="noopener noreferrer">
                <img
                    class="project-item-immengold-image"
                    src="<?= e(photo_library_image_variant_url(photo_library_image_url('20.02_Web', '12_Kerzen_Immengold_Isabelle_Kraemer/Kerzen_gemischt.jpg'), 'preview')) ?>"
                    alt=""
                    loading="lazy"
                    decoding="async"
                >
                <span class="project-item-immengold-content">
                    <p>Immengoldkerzen sind handgezogene Kerzen aus reinem Bienenwachs, dem Gold der Imme.</p>
                </span>
            </a>
        </div>
    </section>

</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
