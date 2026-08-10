<?php

declare(strict_types=1);

$pageTitle = 'Kontakt - Marcus Reiser';
$pageDescription = 'Kontaktformular für Anfragen zu IT-Vertrieb, Projekten und Zusammenarbeit.';
$bodyClass = 'subpage';
$currentPage = 'contact';

$errors = (array) ($_SESSION['form_errors'] ?? []);
$success = flash('success');
$hasPrivacyConsentError = false;
foreach ($errors as $error) {
    if (stripos((string) $error, 'Datenschutzrichtlinien') !== false) {
        $hasPrivacyConsentError = true;
        break;
    }
}

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="subpage-top">
  <?php
  $navContext = 'subpage';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>
</header>

<main class="subpage-main wrap">
  <section class="contact-wrap" aria-label="Kontaktformular">
    <div class="panel">
      <p class="eyebrow-lite">Kontakt</p>
      <h1>Kontaktformular</h1>
      <p>
        Du möchtest mich für IT-Vertrieb, Projekte oder eine Zusammenarbeit kontaktieren?
        Dann sende mir hier direkt deine Nachricht.
      </p>
      <div class="contact-facts">
        <p><strong>Standort:</strong> Weimar / Legefeld</p>
        <p><strong>E-Mail:</strong> <a href="mailto:info@marcusreiser.de">info@marcusreiser.de</a></p>
      </div>
    </div>

    <div class="panel">
      <?php if ($success !== null): ?>
        <p class="notice success"><?= e($success) ?></p>
      <?php endif; ?>

      <?php if ($errors !== []): ?>
        <div class="notice error">
          <strong>Bitte prüfe deine Eingabe:</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= e((string) $error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="/contact" class="form-grid" novalidate>
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

        <label for="name">Name</label>
        <input id="name" name="name" type="text" value="<?= old('name') ?>" required minlength="2">

        <label for="email">E-Mail</label>
        <input id="email" name="email" type="email" value="<?= old('email') ?>" required>

        <label for="message">Nachricht</label>
        <textarea id="message" name="message" rows="6" required minlength="20"><?= old('message') ?></textarea>

        <div class="consent-wrap<?= $hasPrivacyConsentError ? ' has-error' : '' ?>">
          <input
            id="privacy_accepted"
            name="privacy_accepted"
            type="checkbox"
            value="1"
            required
            <?= old('privacy_accepted') === '1' ? 'checked' : '' ?>
          >
          <label for="privacy_accepted">
            Hiermit akzeptiere ich die <a href="/datenschutz" target="_blank" rel="noopener noreferrer">Datenschutzrichtlinien</a>.
          </label>
          <?php if ($hasPrivacyConsentError): ?>
            <p class="consent-error">Bitte zuerst die Datenschutzrichtlinien bestätigen.</p>
          <?php endif; ?>
        </div>

        <input class="hp" type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">

        <button type="submit" class="btn btn-primary">Anfrage senden</button>
      </form>
    </div>
  </section>
</main>

<?php
unset($_SESSION['form_errors'], $_SESSION['form_old']);
require BASE_PATH . '/Components/layout/footer.php';
