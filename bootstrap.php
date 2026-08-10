<?php

declare(strict_types=1);

// Gemeinsame Bootstrap-Datei mit Basis-Konstanten und Formular-Helfern.
define('BASE_PATH', __DIR__);
define('DATA_PATH', BASE_PATH . '/data');

$isHttps = (
	(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
	|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
);

// Session-Cookies sicher konfigurieren und Session einmalig starten.
if (session_status() !== PHP_SESSION_ACTIVE) {
	ini_set('session.use_strict_mode', '1');
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'secure' => $isHttps,
		'httponly' => true,
		'samesite' => 'Lax',
	]);
	session_start();
}

/**
 * Liest eine einfache KEY=VALUE .env-Datei und schreibt Werte in die Laufzeitumgebung.
 */
function load_env_file(string $filePath): void
{
	if (!is_file($filePath)) {
		return;
	}

	$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if (!is_array($lines)) {
		return;
	}

	foreach ($lines as $line) {
		$trimmed = trim($line);
		if ($trimmed === '' || str_starts_with($trimmed, '#')) {
			continue;
		}

		$parts = explode('=', $trimmed, 2);
		if (count($parts) !== 2) {
			continue;
		}

		$key = trim($parts[0]);
		$value = trim($parts[1]);

		if ($key === '') {
			continue;
		}

		$_ENV[$key] = $value;
		$_SERVER[$key] = $value;
		putenv($key . '=' . $value);
	}
}

/**
 * Liest einen Konfigurationswert aus ENV/Server/putenv.
 */
function app_env(string $key, string $default = ''): string
{
	$value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
	return is_string($value) && $value !== '' ? $value : $default;
}

load_env_file(BASE_PATH . '/.env');

/**
 * HTML-sicheres Escaping.
 */
function e(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Liefert den CSRF-Token und erzeugt ihn bei Bedarf.
 */
function csrf_token(): string
{
	if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}

	return $_SESSION['csrf_token'];
}

/**
 * Prüft den CSRF-Token gegen den Session-Wert.
 */
function csrf_token_is_valid(string $token): bool
{
	$sessionToken = (string) ($_SESSION['csrf_token'] ?? '');
	return $sessionToken !== '' && $token !== '' && hash_equals($sessionToken, $token);
}

/**
 * Ersetzt den Token nach erfolgreichem Submit.
 */
function csrf_token_rotate(): void
{
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Merkt sich alte Formulardaten über Redirect hinweg.
 *
 * @param array<string, string> $values
 */
function set_old(array $values): void
{
	$_SESSION['form_old'] = $values;
}

/**
 * Liest einen alten Formularwert aus.
 */
function old(string $key): string
{
	$old = (array) ($_SESSION['form_old'] ?? []);
	$value = (string) ($old[$key] ?? '');
	return e($value);
}

/**
 * Setzt eine Flash-Nachricht.
 */
function set_flash(string $key, string $message): void
{
	$_SESSION['flash'][$key] = $message;
}

/**
 * Holt und entfernt eine Flash-Nachricht.
 */
function flash(string $key): ?string
{
	if (!isset($_SESSION['flash'][$key]) || !is_string($_SESSION['flash'][$key])) {
		return null;
	}

	$message = $_SESSION['flash'][$key];
	unset($_SESSION['flash'][$key]);
	return $message;
}

/**
 * Schreibt Statistikdaten als JSON-Datei.
 *
 * @param array<string, mixed> $payload
 */
function write_json_file(string $filePath, array $payload): void
{
	$dir = dirname($filePath);
	if (!is_dir($dir)) {
		mkdir($dir, 0775, true);
	}

	$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	if ($json === false) {
		return;
	}

	file_put_contents($filePath, $json, LOCK_EX);
}

/**
 * Liest eine JSON-Datei als assoziatives Array.
 *
 * @return array<string, mixed>
 */
function read_json_file(string $filePath): array
{
	if (!is_file($filePath)) {
		return [];
	}

	$raw = file_get_contents($filePath);
	if (!is_string($raw) || $raw === '') {
		return [];
	}

	$decoded = json_decode($raw, true);
	return is_array($decoded) ? $decoded : [];
}

/**
 * Zählt Seitenbesuche für Gesamt-, Pfad- und Tagesstatistik.
 */
function track_page_visit(string $path): void
{
	$statsPath = DATA_PATH . '/logs/visits.json';
	$stats = read_json_file($statsPath);

	$total = (int) ($stats['total'] ?? 0);
	$paths = is_array($stats['paths'] ?? null) ? $stats['paths'] : [];
	$daily = is_array($stats['daily'] ?? null) ? $stats['daily'] : [];
	$uniqueDaily = is_array($stats['unique_daily'] ?? null) ? $stats['unique_daily'] : [];

	$total++;
	$paths[$path] = (int) ($paths[$path] ?? 0) + 1;

	$today = date('Y-m-d');
	$daily[$today] = (int) ($daily[$today] ?? 0) + 1;

	$sessionDay = (string) ($_SESSION['visit_counted_day'] ?? '');
	if ($sessionDay !== $today) {
		$uniqueDaily[$today] = (int) ($uniqueDaily[$today] ?? 0) + 1;
		$_SESSION['visit_counted_day'] = $today;
	}

	$stats['total'] = $total;
	$stats['paths'] = $paths;
	$stats['daily'] = $daily;
	$stats['unique_daily'] = $uniqueDaily;
	$stats['last_visit'] = date('c');

	write_json_file($statsPath, $stats);
}

/**
 * Liefert die aktuelle Besuchsstatistik.
 *
 * @return array<string, mixed>
 */
function get_visit_stats(): array
{
	$statsPath = DATA_PATH . '/logs/visits.json';
	$stats = read_json_file($statsPath);

	if ($stats === []) {
		return [
			'total' => 0,
			'paths' => [],
			'daily' => [],
			'unique_daily' => [],
			'last_visit' => null,
		];
	}

	return [
		'total' => (int) ($stats['total'] ?? 0),
		'paths' => is_array($stats['paths'] ?? null) ? $stats['paths'] : [],
		'daily' => is_array($stats['daily'] ?? null) ? $stats['daily'] : [],
		'unique_daily' => is_array($stats['unique_daily'] ?? null) ? $stats['unique_daily'] : [],
		'last_visit' => isset($stats['last_visit']) && is_string($stats['last_visit']) ? $stats['last_visit'] : null,
	];
}

/**
 * Speichert Bestellformularwerte für Redirect-Back.
 *
 * @param array<string, string> $values
 */
function set_order_old(array $values): void
{
	$_SESSION['order_old'] = $values;
}

/**
 * Liest einen alten Bestellwert aus.
 */
function order_old(string $key): string
{
	$old = (array) ($_SESSION['order_old'] ?? []);
	$value = (string) ($old[$key] ?? '');
	return e($value);
}

/**
 * Speichert eine Kalender-Bestellung als JSON-Zeile.
 *
 * @param array<string, mixed> $order
 */
function persist_calendar_order(array $order): void
{
	$messagesDir = DATA_PATH . '/messages';
	if (!is_dir($messagesDir)) {
		mkdir($messagesDir, 0775, true);
	}

	$line = json_encode($order, JSON_UNESCAPED_UNICODE) . PHP_EOL;
	if ($line === false) {
		return;
	}

	file_put_contents($messagesDir . '/orders.log', $line, FILE_APPEND | LOCK_EX);
}

/**
 * Liest alle gespeicherten Kalender-Bestellungen.
 *
 * @return array<int, array<string, mixed>>
 */
function get_calendar_orders(): array
{
	$filePath = DATA_PATH . '/messages/orders.log';
	if (!is_file($filePath)) {
		return [];
	}

	$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if (!is_array($lines)) {
		return [];
	}

	$orders = [];
	foreach ($lines as $line) {
		$decoded = json_decode($line, true);
		if (is_array($decoded)) {
			$orders[] = $decoded;
		}
	}

	return array_reverse($orders);
}

/**
 * Verarbeitet eine Kalender-Bestellung und leitet zurück zur Kalenderseite.
 */
function handle_calendar_order_submission(): void
{
	$name = trim((string) ($_POST['name'] ?? ''));
	$email = trim((string) ($_POST['email'] ?? ''));
	$quantityRaw = trim((string) ($_POST['quantity'] ?? '1'));
	$message = trim((string) ($_POST['message'] ?? ''));
	$website = trim((string) ($_POST['website'] ?? ''));
	$privacyAccepted = (string) ($_POST['privacy_accepted'] ?? '');
	$token = (string) ($_POST['_csrf'] ?? '');

	set_order_old([
		'name' => $name,
		'email' => $email,
		'quantity' => $quantityRaw,
		'message' => $message,
		'privacy_accepted' => $privacyAccepted === '1' ? '1' : '',
	]);

	$errors = [];
	if (!csrf_token_is_valid($token)) {
		$errors[] = 'Sicherheitsprüfung fehlgeschlagen.';
	}
	if ($website !== '') {
		$errors[] = 'Bestellung konnte nicht verarbeitet werden.';
	}
	if (mb_strlen($name) < 2) {
		$errors[] = 'Bitte gib einen gültigen Namen ein.';
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
	}

	$quantity = (int) $quantityRaw;
	if (!preg_match('/^\d+$/', $quantityRaw) || $quantity < 1 || $quantity > 20) {
		$errors[] = 'Bitte gib eine Stückzahl zwischen 1 und 20 ein.';
	}
	if ($privacyAccepted !== '1') {
		$errors[] = 'Bitte akzeptiere zuerst die Datenschutzrichtlinien.';
	}

	if ($errors !== []) {
		$_SESSION['order_errors'] = $errors;
		header('Location: /kalender', true, 302);
		exit;
	}

	$safeName = str_replace(["\r", "\n"], '', $name);
	$safeEmail = str_replace(["\r", "\n"], '', $email);

	persist_calendar_order([
		'timestamp' => date('c'),
		'name' => $safeName,
		'email' => $safeEmail,
		'quantity' => $quantity,
		'message' => $message,
		'ip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
	]);

	set_flash('order_success', 'Danke! Deine Bestellung wurde gespeichert. Ich melde mich per E-Mail bei dir.');
	unset($_SESSION['order_old'], $_SESSION['order_errors']);
	csrf_token_rotate();

	header('Location: /kalender', true, 302);
	exit;
}

/**
 * Prueft, ob eine Session fuer die interne Statistik angemeldet ist.
 */
function is_stats_authenticated(): bool
{
	return (bool) ($_SESSION['stats_authenticated'] ?? false);
}

/**
 * Verarbeitet das Statistik-Login und setzt bei Erfolg die Session.
 */
function handle_statistics_login_submission(): void
{
	$token = (string) ($_POST['_csrf'] ?? '');
	$password = (string) ($_POST['password'] ?? '');
	$configuredPassword = app_env('STATS_PASSWORD', '');

	if (!csrf_token_is_valid($token)) {
		set_flash('stats_login_error', 'Sicherheitspruefung fehlgeschlagen.');
		header('Location: /statistik-login', true, 302);
		exit;
	}

	if ($configuredPassword === '') {
		set_flash('stats_login_error', 'Kein Statistik-Passwort konfiguriert. Bitte STATS_PASSWORD in .env setzen.');
		header('Location: /statistik-login', true, 302);
		exit;
	}

	if (hash_equals($configuredPassword, $password)) {
		$_SESSION['stats_authenticated'] = true;
		set_flash('stats_login_success', 'Erfolgreich angemeldet.');
		csrf_token_rotate();
		header('Location: /statistik', true, 302);
		exit;
	}

	set_flash('stats_login_error', 'Passwort ist nicht korrekt.');
	header('Location: /statistik-login', true, 302);
	exit;
}

/**
 * Meldet die Statistik-Session ab.
 */
function handle_statistics_logout(): void
{
	unset($_SESSION['stats_authenticated']);
	set_flash('stats_login_success', 'Du wurdest abgemeldet.');
	header('Location: /statistik-login', true, 302);
	exit;
}

/**
 * Verarbeitet das Kontaktformular serverseitig und leitet anschließend zurück.
 */
function handle_contact_form_submission(): void
{
	// Rohdaten aus dem Request lesen und für Redirect-Validierung zwischenspeichern.
	$name = trim((string) ($_POST['name'] ?? ''));
	$email = trim((string) ($_POST['email'] ?? ''));
	$message = trim((string) ($_POST['message'] ?? ''));
	$website = trim((string) ($_POST['website'] ?? ''));
	$privacyAccepted = (string) ($_POST['privacy_accepted'] ?? '');
	$token = (string) ($_POST['_csrf'] ?? '');

	set_old([
		'name' => $name,
		'email' => $email,
		'message' => $message,
		'privacy_accepted' => $privacyAccepted === '1' ? '1' : '',
	]);

	$errors = [];
	// Sicherheits- und Plausibilitätsprüfungen für alle Pflichtfelder.
	if (!csrf_token_is_valid($token)) {
		$errors[] = 'Sicherheitsprüfung fehlgeschlagen.';
	}
	if ($website !== '') {
		$errors[] = 'Anfrage konnte nicht verarbeitet werden.';
	}
	if (mb_strlen($name) < 2) {
		$errors[] = 'Bitte gib einen gültigen Namen ein.';
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
	}
	if (mb_strlen($message) < 20) {
		$errors[] = 'Bitte gib mindestens 20 Zeichen ein.';
	}
	if ($privacyAccepted !== '1') {
		$errors[] = 'Bitte akzeptiere zuerst die Datenschutzrichtlinien.';
	}

	if ($errors !== []) {
		// Fehler werden in der Session gehalten und nach Redirect auf /contact angezeigt.
		$_SESSION['form_errors'] = $errors;
		header('Location: /contact', true, 302);
		exit;
	}

	// Header-Injection verhindern, bevor Werte in Log oder Mail landen.
	$safeName = str_replace(["\r", "\n"], '', $name);
	$safeEmail = str_replace(["\r", "\n"], '', $email);

	// Kontaktanfrage immer lokal protokollieren.
	$messagesDir = DATA_PATH . '/messages';
	if (!is_dir($messagesDir)) {
		mkdir($messagesDir, 0775, true);
	}
	$entry = sprintf("[%s] %s <%s>\n%s\n----\n", date('c'), $safeName, $safeEmail, $message);
	file_put_contents($messagesDir . '/contact.log', $entry, FILE_APPEND);

	// Versand per mail() ist best effort; das lokale Log bleibt die verlässliche Basis.
	$mailSent = false;
	$to = 'info@marcusreiser.de';
	$subject = 'Kontaktformular marcusreiser.de | Neue Anfrage von ' . $safeName;
	$body = "Name: {$safeName}\nE-Mail: {$safeEmail}\n\nNachricht:\n{$message}";
	$headers = "From: Marcus Reiser <info@marcusreiser.de>\r\n";
	$headers .= "Reply-To: <{$safeEmail}>\r\n";
	$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

	try {
		$mailSent = mail($to, $subject, $body, $headers);
	} catch (\Throwable $exception) {
		$mailSent = false;
	}

	if ($mailSent) {
		set_flash('success', 'Danke! Deine Nachricht wurde erfolgreich gesendet.');
	} else {
		set_flash('success', 'Danke! Deine Nachricht wurde gespeichert. Der E-Mail-Versand konnte aktuell nicht bestätigt werden.');
	}

	// Aufräumen und Token-Rotation verhindern Mehrfach-Submit mit altem CSRF-Token.
	unset($_SESSION['form_old'], $_SESSION['form_errors']);
	csrf_token_rotate();

	header('Location: /contact', true, 302);
	exit;
}
