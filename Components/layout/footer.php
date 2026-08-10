<?php

declare(strict_types=1);

// Optionales ID-Attribut erlaubt Sprungmarken oder seitenbezogene Hooks.
$footerId = isset($footerId) && is_string($footerId) ? trim($footerId) : '';
$footerIdAttribute = $footerId !== ''
    ? ' id="' . htmlspecialchars($footerId, ENT_QUOTES, 'UTF-8') . '"'
    : '';
?>
<footer class="site-footer"<?= $footerIdAttribute ?>>
  <div class="wrap footer-grid">
    <!-- Kontaktblock mit direktem Mail-Link und Formular-Einstieg. -->
    <div>
      <h2>Kontakt</h2>
      <p>
        Marcus Reiser<br>
        Weimar / Legefeld<br>
        <a href="mailto:info@marcusreiser.de">info@marcusreiser.de</a>
      </p>
      <a href="/contact">Kontaktformular öffnen</a>
    </div>

    <nav aria-label="Rechtliches">
      <h2>Rechtliches</h2>
      <a href="/impressum">Impressum</a>
      <a href="/datenschutz">Datenschutz</a>
    </nav>

    <nav aria-label="Social Media">
      <h2>Social Media</h2>
      <a href="#">Instagram</a>
      <a href="#">GitHub</a>
    </nav>
  </div>
</footer>
</body>
</html>
