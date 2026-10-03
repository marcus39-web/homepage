<?php declare(strict_types=1);

$pageTitle = "Fotokalender 2027 – Marcus Reiser";
$pageDescription = "Jahreskalender 2027 mit Motiven aus Weimar, Natur und Architektur.";
$navContext = "subpage";
$bodyClass = "subpage";
$orderErrors = (array) ($_SESSION['order_errors'] ?? []);
$orderSuccess = flash('order_success');
$calendarMotifs = calendar_motif_catalog();
$calendarMonths = calendar_month_defaults();
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="subpage-top">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
    <div class="wrap subpage-head">
        <p class="eyebrow-lite">Kalender 2027</p>
        <h1>Fotokalender mit Motiven aus Weimar</h1>
        <p>
            Der Jahreskalender 2027 enthält 12 ausgewählte Fotografien aus Natur, Architektur,
            Stadtmotiven und besonderen Momenten. Gedruckt auf hochwertigem Papier – ideal für die eigene Wand
            oder als Geschenk.
        </p>
    </div>
</div>

<div class="subpage-main wrap">

    <section class="calendar-builder" aria-label="Kalender-Vorschau und Motivauswahl">
        <div class="calendar-preview-sheet" aria-label="Vorschau eines Kalenderblatts">
            <img
                class="calendar-preview-image"
                id="calendar-preview-image"
                src="<?= e($calendarMotifs[$calendarMonths['Januar']]['url']) ?>"
                alt="<?= e($calendarMotifs[$calendarMonths['Januar']]['label']) ?>"
            >
            <div class="calendar-sheet-details">
                <div class="calendar-sheet-heading">
                    <h2 id="calendar-preview-month-label">Januar</h2>
                    <span>2027</span>
                </div>
                <div class="calendar-weekdays" aria-hidden="true">
                    <span>Mo</span><span>Di</span><span>Mi</span><span>Do</span><span>Fr</span><span>Sa</span><span>So</span>
                </div>
                <div class="calendar-preview-days" id="calendar-preview-days" aria-hidden="true"></div>
            </div>
        </div>

        <div class="calendar-builder-copy">
            <p class="eyebrow-lite">Dein Kalender 2027</p>
            <h2>Zwölf Motive aus Weimar</h2>
            <p>Wähle für jeden Monat ein Bild. Deine Auswahl bleibt auf diesem Gerät gespeichert und kann jederzeit geändert werden.</p>
            <label for="calendar-preview-month">Kalendervorschau</label>
            <select id="calendar-preview-month">
                <?php foreach ($calendarMonths as $month => $motifId): ?>
                    <option value="<?= e($month) ?>"><?= e($month) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="calendar-date-controls">
                <label for="calendar-state">Feiertage und Ferien für</label>
                <select id="calendar-state">
                    <option value="BW">Baden-Württemberg</option>
                    <option value="BY">Bayern</option>
                    <option value="BE">Berlin</option>
                    <option value="BB">Brandenburg</option>
                    <option value="HB">Bremen</option>
                    <option value="HH">Hamburg</option>
                    <option value="HE">Hessen</option>
                    <option value="MV">Mecklenburg-Vorpommern</option>
                    <option value="NI">Niedersachsen</option>
                    <option value="NW">Nordrhein-Westfalen</option>
                    <option value="RP">Rheinland-Pfalz</option>
                    <option value="SL">Saarland</option>
                    <option value="SN">Sachsen</option>
                    <option value="ST">Sachsen-Anhalt</option>
                    <option value="SH">Schleswig-Holstein</option>
                    <option value="TH" selected>Thüringen</option>
                </select>
                <label class="calendar-school-toggle" for="calendar-show-school-breaks">
                    <input id="calendar-show-school-breaks" type="checkbox">
                    Schulferien hervorheben
                </label>
                <p class="calendar-date-note">Feiertage tragen das Länder-Kürzel. Ferien nach KMK 2026/27 und 2027/28; bewegliche Ferientage sind nicht enthalten. * kennzeichnet regionale Feiertage.</p>
                <div class="calendar-legend" aria-label="Farblegende">
                    <span><i class="calendar-legend-swatch is-saturday"></i>Samstag</span>
                    <span><i class="calendar-legend-swatch is-sunday"></i>Sonntag</span>
                    <span><i class="calendar-legend-swatch is-holiday"></i>Feiertag</span>
                    <span><i class="calendar-legend-swatch is-school-break"></i>Ferien</span>
                </div>
            </div>

            <a class="btn btn-primary" href="#monatsmotive">Monatsmotive auswählen</a>
        </div>
    </section>

    <section class="calendar-selection" id="monatsmotive" aria-labelledby="calendar-selection-title">
        <div class="section-head">
            <h2 id="calendar-selection-title">Deine Monatsmotive</h2>
            <p>Alle ausgewählten Bilder bleiben sichtbar und lassen sich einzeln austauschen.</p>
        </div>

        <div class="calendar-month-grid">
            <?php foreach ($calendarMonths as $month => $selectedMotifId): ?>
                <?php $selectedMotif = $calendarMotifs[$selectedMotifId]; ?>
                <article class="calendar-month-card">
                    <div class="calendar-month-image-wrap">
                        <img
                            class="calendar-month-image"
                            data-calendar-image
                            src="<?= e($selectedMotif['url']) ?>"
                            alt="<?= e($month . ': ' . $selectedMotif['label']) ?>"
                            loading="lazy"
                        >
                    </div>
                    <div class="calendar-month-content">
                        <h3><?= e($month) ?></h3>
                        <p data-calendar-label><?= e($selectedMotif['label']) ?></p>
                        <div class="calendar-month-calendar">
                            <div class="calendar-weekdays" aria-hidden="true">
                                <span>Mo</span><span>Di</span><span>Mi</span><span>Do</span><span>Fr</span><span>Sa</span><span>So</span>
                            </div>
                            <div class="calendar-preview-days" data-month-calendar="<?= e($month) ?>" aria-hidden="true"></div>
                        </div>
                        <label for="calendar-motif-<?= (int) array_search($month, array_keys($calendarMonths), true) ?>">Motiv für <?= e($month) ?></label>
                        <select
                            id="calendar-motif-<?= (int) array_search($month, array_keys($calendarMonths), true) ?>"
                            class="calendar-motif-select"
                            data-calendar-month="<?= e($month) ?>"
                        >
                            <?php foreach ($calendarMotifs as $motifId => $motif): ?>
                                <option value="<?= e($motifId) ?>" data-image-url="<?= e($motif['url']) ?>" data-image-alt="<?= e($motif['label']) ?>" <?= $motifId === $selectedMotifId ? 'selected' : '' ?>><?= e($motif['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Druckinformationen -->
    <section class="panel" style="margin-top: 2rem;">
        <h2>Druckinformationen</h2>
        <p>
            Der Kalender wird auf hochwertigem 250g-Papier gedruckt und verfügt über eine stabile Spiralbindung.
            Format: DIN A4 hochkant. Die Farben werden professionell kalibriert und entsprechen den Originalmotiven.
        </p>

        <ul class="project-list">
            <li>Format: DIN A4 (21 × 29,7 cm)</li>
            <li>Papier: 250g Premium-Bilderdruck</li>
            <li>Bindung: Metallspirale</li>
            <li>12 Monatsmotive + Titelblatt</li>
            <li>Optional: persönliche Widmung auf der Rückseite</li>
        </ul>
    </section>

    <!-- Bestellanfrage -->
    <section class="panel calendar-panel" id="bestellen" aria-labelledby="order-title">
        <h2 id="order-title">Kalender anfragen</h2>
        <p>
            Sende mir deine Anfrage. Ich melde mich zur Verfügbarkeit und zur finalen Abstimmung bei dir.
        </p>

        <?php if ($orderSuccess !== null): ?>
            <p class="notice success" role="status"><?= e($orderSuccess) ?></p>
        <?php endif; ?>

        <?php if ($orderErrors !== []): ?>
            <div class="notice error" role="alert">
                <strong>Bitte prüfe deine Anfrage:</strong>
                <ul>
                    <?php foreach ($orderErrors as $error): ?>
                        <li><?= e((string) $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/kalender-bestellung" class="form-grid" novalidate>
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <?php foreach ($calendarMonths as $month => $motifId): ?>
                <input type="hidden" name="motifs[<?= e($month) ?>]" value="<?= e($motifId) ?>" data-order-motif="<?= e($month) ?>">
            <?php endforeach; ?>

            <label for="order_name">Name</label>
            <input id="order_name" name="name" type="text" value="<?= order_old('name') ?>" required minlength="2" autocomplete="name">

            <label for="order_email">E-Mail</label>
            <input id="order_email" name="email" type="email" value="<?= order_old('email') ?>" required autocomplete="email">

            <label for="order_quantity">Stückzahl</label>
            <input id="order_quantity" name="quantity" type="number" min="1" max="20" value="<?= order_old('quantity') !== '' ? order_old('quantity') : '1' ?>" required>

            <label for="order_message">Nachricht (optional)</label>
            <textarea id="order_message" name="message" rows="4"><?= order_old('message') ?></textarea>

            <div class="consent-wrap">
                <input id="order_privacy_accepted" name="privacy_accepted" type="checkbox" value="1" required <?= order_old('privacy_accepted') === '1' ? 'checked' : '' ?>>
                <label for="order_privacy_accepted">Ich akzeptiere die <a href="/datenschutz" target="_blank" rel="noopener noreferrer">Datenschutzhinweise</a> zur Bearbeitung meiner Anfrage.</label>
            </div>

            <input class="hp" type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">

            <button type="submit" class="btn btn-secondary">Anfrage absenden</button>
        </form>
    </section>

</div>

<?php unset($_SESSION['order_errors'], $_SESSION['order_old']); ?>

<dialog class="photo-lightbox" id="lightbox">
    <img id="lightbox-img" class="photo-lightbox-image" src="" alt="">
    <button class="photo-lightbox-close" id="lightbox-close">&times;</button>
</dialog>

<script src="/public/assets/js/gallery-lightbox.js"></script>
<script src="/public/assets/js/calendar-selection.js" defer></script>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
