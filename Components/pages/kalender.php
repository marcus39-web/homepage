<?php

declare(strict_types=1);

$pageTitle = 'Kalender 2026 - Marcus Reiser';
$pageDescription = 'Fotokalender 2026 von Marcus Reiser mit Motiven aus Thüringen.';
$bodyClass = 'subpage';
$currentPage = 'kalender';

$orderErrors = (array) ($_SESSION['order_errors'] ?? []);
$orderSuccess = flash('order_success');

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
    <p class="eyebrow-lite">Fotokalender</p>
    <h1>Kalender 2026 - Thüringen in Bildern</h1>
    <p>12 ausgewählte Motive aus der Canon R10. Hochwertiger Druck, ideal als Geschenk oder für Zuhause.</p>
    <div class="hero-actions">
      <a class="btn btn-secondary" href="#bestellen">Jetzt bestellen</a>
      <a class="btn btn-primary" href="/">Zur Startseite</a>
    </div>
  </section>

  <section class="calendar-teaser panel calendar-panel" aria-label="Kalender Details">
    <div class="calendar-text">
      <h2>Was dich erwartet</h2>
      <ul class="project-list">
        <li>12 Monatsblätter mit ausgewählten Motiven</li>
        <li>Regionale Schwerpunkte aus Thüringen</li>
        <li>Hochwertiges Papier mit starker Farbwiedergabe</li>
        <li>Direkte Bestellung per Kontaktanfrage</li>
      </ul>
    </div>
    <div class="calendar-image">
      <img src="/public/assets/images/calendar-2026.svg" alt="Kalender 2026 Vorschau">
    </div>
  </section>

  <section class="panel calendar-panel" id="bestellen" aria-labelledby="order-title">
    <h2 id="order-title">Kalender bestellen</h2>
    <p>Trage deine Daten ein. Ich melde mich bei dir zur finalen Abstimmung der Bestellung.</p>

    <?php if ($orderSuccess !== null): ?>
      <p class="notice success"><?= e($orderSuccess) ?></p>
    <?php endif; ?>

    <?php if ($orderErrors !== []): ?>
      <div class="notice error">
        <strong>Bitte prüfe deine Bestellung:</strong>
        <ul>
          <?php foreach ($orderErrors as $error): ?>
            <li><?= e((string) $error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="/kalender-bestellung" class="form-grid" novalidate>
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

      <label for="order_name">Name</label>
      <input id="order_name" name="name" type="text" value="<?= order_old('name') ?>" required minlength="2">

      <label for="order_email">E-Mail</label>
      <input id="order_email" name="email" type="email" value="<?= order_old('email') ?>" required>

      <label for="order_quantity">Stückzahl</label>
      <input id="order_quantity" name="quantity" type="number" min="1" max="20" value="<?= order_old('quantity') !== '' ? order_old('quantity') : '1' ?>" required>

      <label for="order_message">Nachricht (optional)</label>
      <textarea id="order_message" name="message" rows="4"><?= order_old('message') ?></textarea>

      <div class="consent-wrap">
        <input
          id="order_privacy_accepted"
          name="privacy_accepted"
          type="checkbox"
          value="1"
          required
          <?= order_old('privacy_accepted') === '1' ? 'checked' : '' ?>
        >
        <label for="order_privacy_accepted">
          Hiermit akzeptiere ich die <a href="/datenschutz" target="_blank" rel="noopener noreferrer">Datenschutzrichtlinien</a>.
        </label>
      </div>

      <input class="hp" type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">

      <button type="submit" class="btn btn-secondary">Verbindlich anfragen</button>
    </form>
  </section>
</main>

<?php unset($_SESSION['order_errors'], $_SESSION['order_old']); ?>
<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
