<?php

declare(strict_types=1);

$pageTitle = 'Statistik Login - Marcus Reiser';
$pageDescription = 'Passwortgeschuetzter Zugang zur internen Statistik.';
$bodyClass = 'subpage';
$currentPage = '';

// Feedback aus dem letzten Login-/Logout-Versuch.
$loginError = flash('stats_login_error');
$loginSuccess = flash('stats_login_success');

require BASE_PATH . '/Components/layout/header.php';
?>
<header class="subpage-top">
  <?php
  $navContext = 'subpage';
  require BASE_PATH . '/Components/layout/nav.php';
  ?>
</header>

<main class="subpage-main wrap">
  <section class="panel" aria-labelledby="stats-login-title">
    <p class="eyebrow-lite">Interner Bereich</p>
    <h1 id="stats-login-title">Statistik Login</h1>
    <p>Bitte Passwort eingeben, um die interne Statistik zu sehen.</p>

    <?php if ($loginSuccess !== null): ?>
      <p class="notice success"><?= e($loginSuccess) ?></p>
    <?php endif; ?>

    <?php if ($loginError !== null): ?>
      <p class="notice error"><?= e($loginError) ?></p>
    <?php endif; ?>

    <!-- Passwort wird serverseitig gegen STATS_PASSWORD aus .env geprueft. -->
    <form method="post" action="/statistik-login" class="form-grid" novalidate>
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

      <label for="stats_password">Passwort</label>
      <input id="stats_password" name="password" type="password" required autocomplete="current-password">

      <button type="submit" class="btn btn-primary">Anmelden</button>
    </form>
  </section>
</main>

<?php require BASE_PATH . '/Components/layout/footer.php'; ?>
