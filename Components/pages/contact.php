<?php declare(strict_types=1);

$pageTitle = "Kontakt – Marcus Reiser";
$pageDescription = "Kontaktformular für Fotografie, Kalender und IT‑Anfragen.";
$navContext = "subpage";
$bodyClass = "subpage";
$successMessage = flash('success');
$formErrors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
$formErrors = is_array($formErrors) ? $formErrors : [];
require BASE_PATH . '/Components/layout/header.php';
?>

<div class="subpage-top">
    <?php require BASE_PATH . '/Components/layout/nav.php'; ?>
    <div class="wrap subpage-head">
        <p class="eyebrow-lite">Kontakt</p>
        <h1>Schreib mir eine Nachricht</h1>
        <p>
            Für Anfragen zu Fotografie, Kalendern, Projekten oder IT‑Themen kannst du mir jederzeit schreiben.
            Ich melde mich schnellstmöglich zurück.
        </p>
    </div>
</div>

<div class="subpage-main wrap">

    <!-- Profilbereich -->
    <section class="panel contact-profile">
        <div>
            <h2>Marcus Reiser</h2>
            <p>Fotografie & IT · Weimar</p>
            <p class="muted">Hobbyfotografie aus Weimar, Kalenderprojekte und IT-Themen.</p>
        </div>
    </section>

    <!-- Kontaktformular -->
    <section class="panel contact-form">
        <h2>Kontaktformular</h2>

        <?php if (is_string($successMessage) && $successMessage !== ''): ?>
            <p class="notice success" role="status"><?= e($successMessage) ?></p>
        <?php endif; ?>

        <?php if ($formErrors !== []): ?>
            <div class="notice error" role="alert">
                <ul>
                    <?php foreach ($formErrors as $formError): ?>
                        <li><?= e((string) $formError) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/contact" method="post" class="form-grid">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

            <div class="form-field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= old('name') ?>" minlength="2" required>
            </div>

            <div class="form-field">
                <label for="email">E‑Mail</label>
                <input type="email" id="email" name="email" value="<?= old('email') ?>" required>
            </div>

            <div class="form-field full">
                <label for="message">Nachricht</label>
                <textarea id="message" name="message" rows="6" minlength="20" required><?= old('message') ?></textarea>
            </div>

            <!-- Honeypot -->
            <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">

            <div class="consent-wrap">
                <input type="checkbox" id="privacy_accepted" name="privacy_accepted" value="1" <?= old('privacy_accepted') === '1' ? 'checked' : '' ?> required>
                <label for="privacy_accepted">Ich habe die <a href="/datenschutz">Datenschutzerklärung</a> gelesen und stimme der Verarbeitung meiner Angaben zur Bearbeitung der Anfrage zu.</label>
            </div>

            <button type="submit" class="btn btn-primary">Nachricht senden</button>
        </form>
    </section>

    <!-- DSGVO -->
    <section class="panel contact-dsgvo">
        <h2>Datenschutz</h2>
        <p>
            Deine Daten werden ausschließlich zur Bearbeitung deiner Anfrage verwendet.
            Die Nachricht wird auf dem Webspace gespeichert und zur Zustellung an info@marcusreiser.de übermittelt.
            Bei aktiviertem E-Mail-Versand über Resend wird der Dienst zur Übermittlung eingesetzt.
            Weitere Informationen findest du in der
            <a href="/datenschutz">Datenschutzerklärung</a>.
        </p>
    </section>

</div>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
