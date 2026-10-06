<?php declare(strict_types=1);

$feedbackSuccess = flash('feedback_success');
$feedbackErrors = (array) ($_SESSION['feedback_errors'] ?? []);
$feedbackOld = (array) ($_SESSION['feedback_old'] ?? []);
$totalPageViews = (int) get_visit_stats()['total'];
unset($_SESSION['feedback_errors'], $_SESSION['feedback_old']);

$feedbackRequestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$feedbackPath = parse_url($feedbackRequestUri, PHP_URL_PATH);
$feedbackQuery = parse_url($feedbackRequestUri, PHP_URL_QUERY);
$feedbackReturnTo = is_string($feedbackPath) && $feedbackPath !== '' ? $feedbackPath : '/';
if (is_string($feedbackQuery) && $feedbackQuery !== '') {
    $feedbackReturnTo .= '?' . $feedbackQuery;
}
?>

<footer class="site-footer">
    <div class="footer-grid wrap">

        <div>
            <h2>Marcus Reiser</h2>
            <p>Fotografie & IT · Legefeld</p>
            <p class="stats-meta">Seitenaufrufe insgesamt: <?= number_format($totalPageViews, 0, ',', '.') ?></p>
            <a href="https://www.instagram.com/Marcus_Fotografie_IT/" target="_blank" rel="noopener noreferrer">Instagram: @Marcus_Fotografie_IT</a>
            <button class="footer-feedback-button" type="button" data-open-feedback>Website-Feedback</button>
        </div>

        <div>
            <h2>Navigation</h2>
            <a href="/galerie">Fotografie</a>
            <a href="/kalender">Kalender</a>
            <a href="/contact">Kontakt</a>
        </div>

        <div>
            <h2>Rechtliches</h2>
            <a href="/impressum">Impressum</a>
            <a href="/datenschutz">Datenschutz</a>
        </div>

    </div>
</footer>
<dialog class="calendar-order-dialog feedback-dialog" id="feedback-dialog" aria-labelledby="feedback-dialog-title" data-open-on-load="<?= ($feedbackErrors !== [] || $feedbackSuccess !== null) ? 'true' : 'false' ?>">
    <button class="calendar-order-dialog-close" type="button" data-close-feedback aria-label="Feedback schließen">&times;</button>
    <section class="calendar-order-content">
        <p class="eyebrow-lite">Rückmeldung zur Testversion</p>
        <h2 id="feedback-dialog-title">Kommentar oder Verbesserungsvorschlag</h2>
        <p>Dein Feedback hilft mir, die Website weiterzuentwickeln.</p>

        <?php if ($feedbackSuccess !== null): ?>
            <p class="notice success" role="status"><?= e($feedbackSuccess) ?></p>
        <?php endif; ?>

        <?php if ($feedbackErrors !== []): ?>
            <div class="notice error" role="alert">
                <ul>
                    <?php foreach ($feedbackErrors as $feedbackError): ?>
                        <li><?= e((string) $feedbackError) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/feedback" method="post" class="form-grid" novalidate>
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="return_to" value="<?= e($feedbackReturnTo) ?>">

            <label for="feedback-name">Name (optional)</label>
            <input id="feedback-name" name="name" type="text" maxlength="100" value="<?= e((string) ($feedbackOld['name'] ?? '')) ?>" autocomplete="name">

            <label for="feedback-email">E-Mail für Rückfragen (optional)</label>
            <input id="feedback-email" name="email" type="email" maxlength="254" value="<?= e((string) ($feedbackOld['email'] ?? '')) ?>" autocomplete="email">

            <label for="feedback-message">Kommentar oder Verbesserungsvorschlag</label>
            <textarea id="feedback-message" name="message" rows="5" minlength="10" maxlength="4000" required><?= e((string) ($feedbackOld['message'] ?? '')) ?></textarea>

            <div class="consent-wrap">
                <input id="feedback-privacy" name="privacy_accepted" type="checkbox" value="1" required <?= ($feedbackOld['privacy_accepted'] ?? '') === '1' ? 'checked' : '' ?>>
                <label for="feedback-privacy">Ich stimme der Verarbeitung meines Feedbacks gemäß der <a href="/datenschutz" target="_blank" rel="noopener noreferrer">Datenschutzerklärung</a> zu.</label>
            </div>

            <input class="hp" type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">

            <button type="submit" class="btn btn-primary">Feedback senden</button>
        </form>
    </section>
</dialog>
<script src="/public/assets/js/lazyload.js" defer></script>
<script src="/public/assets/js/nav.js"></script>
<script src="/public/assets/js/feedback-dialog.js" defer></script>
</body>
</html>
