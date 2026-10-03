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
        <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('07_Blumen', 'Sonnenblumen/Legefeld/10_09_2026/Marcus_Sonnenblumen_2.JPG'), 'gallery')) ?>" alt="Marcus zwischen Sonnenblumen">
    </div>

    <div class="hero-content">
        <div class="hero-logo">
            <img src="/public/assets/images/marcus-reiser-logo.png" alt="Marcus Reiser Fotografie">
        </div>
        <p class="hero-kicker">Fotografie · Kalender · Projekte</p>

        <h1>Willkommen auf meiner Website</h1>

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
                Meine Leidenschaft gilt der Naturfotografie, Stadtmotiven und kreativen Projekten.
                Alle Bilder entstehen mit professionellem Equipment und werden sorgfältig nachbearbeitet.
                In der Galerie findest du ausgewählte Serien und Kategorien.
            </p>
        </div>
    </section>

    <!-- Galerie-Teaser -->
    <section class="wrap">
        <div class="section-head">
            <h2>Galerie</h2>
            <p>Eine Auswahl meiner fotografischen Arbeiten – Natur, Architektur, Tiere und mehr.</p>
        </div>

        <div class="gallery-grid">

            <a href="/galerie" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('05_Weimar_und_Umgebung', 'Weimar_Park/28_08_2026_Park_Allee/Weimarpark_Allee.jpg'), 'preview')) ?>" alt="Weimarpark-Allee">
                <h3>Natur</h3>
            </a>

            <a href="/galerie" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('05_Weimar_und_Umgebung', 'Denkmaeler/Schiller_Göthe_Theater_27_08_2026.JPG'), 'preview')) ?>" alt="Schiller-und-Goethe-Denkmal">
                <h3>Architektur</h3>
            </a>

            <a href="/galerie" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('08_Tiere', 'Voegel/Rotmilan/Legefeld/12_09_2026/Rotmilan_Legefeld_12_09_2026.JPG'), 'preview')) ?>" alt="Rotmilan im Flug">
                <h3>Tiere</h3>
            </a>

            <a href="/galerie" class="gallery-card">
                <img src="<?= e(photo_library_image_variant_url(photo_library_image_url('04_Portraits', 'Marcus/farbe/07_09_2026_schwarzer_Hintergrund.JPG'), 'preview')) ?>" alt="Portrait von Marcus Reiser">
                <h3>Portraits</h3>
            </a>

        </div>

        <a href="/galerie" class="calendar-teaser btn btn-primary">Zur kompletten Galerie</a>
    </section>

    <!-- Kalender-Teaser -->
    <section class="offer-band">
        <div class="wrap">
            <p class="offer-kicker">Jahreskalender 2027</p>
            <h2>Fotokalender mit Motiven aus Weimar</h2>

            <div class="offer-grid">

                <div class="offer-item">
                    <h3>12 Monatsmotive</h3>
                    <p>Hochwertiger Fotokalender mit ausgewählten Bildern aus Natur, Architektur und Stadtleben.</p>
                    <a href="/kalender" class="offer-link">Kalender ansehen</a>
                </div>

                <div class="offer-item">
                    <h3>Kalender 2027</h3>
                    <img src="/public/assets/images/galerie/natur/Ilm/Tiefurt_Park_Denkmal_Wasserspiegel_06.09.2026.JPG" alt="Titelmotiv des Fotokalenders 2027" style="max-width: 180px; margin-top: 0.5rem;">
                    <p>Gedruckt auf Premium-Papier, ideal als Geschenk oder für die eigene Wand.</p>
                    <a href="/kalender" class="offer-link">Mehr erfahren</a>
                </div>

            </div>
        </div>
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
                <h2>Website marcusreiser.de</h2>
                <p>Modulares PHP‑System mit Router, Views, automatisierter Galerie und Statistik.</p>
            </div>

            <div class="project-item panel">
                <h2>Kalenderproduktion</h2>
                <p>Jahreskalender mit eigenen Motiven, Druckvorbereitung und Layout.</p>
            </div>

        </div>
    </section>

</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
