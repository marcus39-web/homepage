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
require_once BASE_PATH . '/src/PhotoLibrary.php';

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
 * Motive, die für die Kalenderauswahl freigegeben sind.
 *
 * @return array<string, array{label: string, url: string}>
 */
function calendar_motif_catalog(): array
{
	$files = [
		'flussbaum' => ['label' => 'Baum im Fluss', 'file' => 'Baum_im Fluss_Tiefurt06.09.2026.JPG'],
		'ente-1' => ['label' => 'Ente am Fluss', 'file' => 'Ente_1.JPG'],
		'bach' => ['label' => 'Bach und bunte Steine', 'file' => 'Ilm_kleiner_Bach_bunter_Stein_2.JPG'],
		'blatt-ausschnitt' => ['label' => 'Blätter auf der Ilm', 'file' => 'Ilm_Blätter_Wasseroberfläche_Ausschnitt_27_08_2026.JPG'],
		'ilm-1' => ['label' => 'Ilm in Weimar', 'file' => 'IMG_2237.JPG'],
		'ente-2' => ['label' => 'Ente am Ufer', 'file' => 'Ente_2.JPG'],
		'sonnenblume' => ['label' => 'Sonnenblume', 'file' => 'Sonnenblume_5.JPG'],
		'ente-4' => ['label' => 'Ente auf der Ilm', 'file' => 'Ente_4.JPG'],
		'parkallee' => ['label' => 'Allee im Weimarpark', 'file' => 'Weimarpark_Allee.jpg'],
		'parkdenkmal' => ['label' => 'Denkmal an der Ilm', 'file' => 'Tiefurt_Park_Denkmal_Wasserspiegel_06.09.2026.JPG'],
		'blatt-gross' => ['label' => 'Blätter auf dem Wasser', 'file' => 'Ilm_Blätter_Wasseroberfläche_groß_27_08_2026.JPG'],
		'ilm-2' => ['label' => 'Ilm-Motiv', 'file' => 'IMG_2254.JPG'],
	];

	foreach ($files as &$motif) {
		$webFile = pathinfo($motif['file'], PATHINFO_FILENAME) . '.webp';
		$motif['url'] = '/public/assets/images/galerie/natur/Ilm/' . rawurlencode($webFile);
		unset($motif['file']);
	}
	unset($motif);

	return $files;
}

/**
 * @return array<string, string>
 */
function calendar_month_defaults(): array
{
	return [
		'Januar' => 'flussbaum',
		'Februar' => 'ente-1',
		'März' => 'bach',
		'April' => 'blatt-ausschnitt',
		'Mai' => 'ilm-1',
		'Juni' => 'ente-2',
		'Juli' => 'sonnenblume',
		'August' => 'ente-4',
		'September' => 'parkallee',
		'Oktober' => 'parkdenkmal',
		'November' => 'blatt-gross',
		'Dezember' => 'ilm-2',
	];
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
	$submittedMotifs = $_POST['motifs'] ?? [];

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

	$allowedMotifs = calendar_motif_catalog();
	$selectedMotifs = [];
	if (!is_array($submittedMotifs)) {
		$errors[] = 'Bitte wähle für jeden Monat ein Motiv aus.';
	} else {
		foreach (calendar_month_defaults() as $month => $defaultMotif) {
			$motif = $submittedMotifs[$month] ?? '';
			if (!is_string($motif) || !isset($allowedMotifs[$motif])) {
				$errors[] = 'Bitte wähle ein gültiges Motiv für ' . $month . ' aus.';
				continue;
			}
			$selectedMotifs[$month] = $motif;
		}
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
		'motifs' => $selectedMotifs,
		'ip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
	]);

	$motifCatalog = calendar_motif_catalog();
	$motifLines = [];
	foreach ($selectedMotifs as $month => $motifId) {
		$motifLines[] = $month . ': ' . $motifCatalog[$motifId]['label'];
	}

	$orderSubject = 'Kalenderanfrage marcusreiser.de | ' . $safeName;
	$orderEmailBody = "Neue Kalenderanfrage\n\nName: {$safeName}\nE-Mail: {$safeEmail}\nStückzahl: {$quantity}\n\nMonatsmotive:\n";
	$orderEmailBody .= implode("\n", $motifLines);
	if ($message !== '') {
		$orderEmailBody .= "\n\nNachricht:\n{$message}";
	}

	$mailSent = send_contact_email_via_resend('info@marcusreiser.de', $safeEmail, $orderSubject, $orderEmailBody);
	if ($mailSent) {
		set_flash('order_success', 'Danke! Deine Kalenderanfrage wurde gespeichert und per E-Mail an info@marcusreiser.de gesendet.');
	} else {
		set_flash('order_success', 'Danke! Deine Kalenderanfrage wurde gespeichert. Die E-Mail-Benachrichtigung an info@marcusreiser.de ist fehlgeschlagen; die Anfrage steht weiterhin in der Statistik.');
	}
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
 * Sendet eine Kontaktanfrage ueber die Resend-API, wenn ein API-Schluessel konfiguriert ist.
 */
function send_contact_email_via_resend(string $recipient, string $replyTo, string $subject, string $message): bool
{
	$apiKey = app_env('RESEND_API_KEY');
	$senderEmail = app_env('RESEND_FROM_EMAIL', 'info@marcusreiser.de');
	if ($apiKey === '' || filter_var($senderEmail, FILTER_VALIDATE_EMAIL) === false) {
		return false;
	}

	$payload = json_encode([
		'from' => 'Marcus Reiser <' . $senderEmail . '>',
		'to' => [$recipient],
		'reply_to' => $replyTo,
		'subject' => $subject,
		'text' => $message,
	], JSON_UNESCAPED_UNICODE);
	if ($payload === false) {
		return false;
	}

	$context = stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => "Authorization: Bearer {$apiKey}\r\nContent-Type: application/json\r\nAccept: application/json\r\n",
			'content' => $payload,
			'timeout' => 15,
			'ignore_errors' => true,
		],
	]);

	$response = @file_get_contents('https://api.resend.com/emails', false, $context);
	$statusLine = $http_response_header[0] ?? '';
	return is_string($response) && preg_match('/\s2\d{2}\s/', $statusLine) === 1;
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

	// Resend wird bevorzugt; ohne API-Schluessel bleibt mail() als Server-Fallback aktiv.
	$to = 'info@marcusreiser.de';
	$subject = 'Kontaktformular marcusreiser.de | Neue Anfrage von ' . $safeName;
	$body = "Name: {$safeName}\nE-Mail: {$safeEmail}\n\nNachricht:\n{$message}";
	$mailSent = false;
	$resendApiKey = app_env('RESEND_API_KEY');
	if ($resendApiKey !== '') {
		$mailSent = send_contact_email_via_resend($to, $safeEmail, $subject, $body);
	} else {
	$headers = "From: Marcus Reiser <info@marcusreiser.de>\r\n";
	$headers .= "Reply-To: <{$safeEmail}>\r\n";
	$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

	try {
		$mailSent = mail($to, $subject, $body, $headers);
	} catch (\Throwable $exception) {
		$mailSent = false;
	}
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
