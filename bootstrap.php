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
 * Verarbeitet das Kontaktformular serverseitig und leitet anschließend zurück.
 */
function handle_contact_form_submission(): void
{
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
		$_SESSION['form_errors'] = $errors;
		header('Location: /contact', true, 302);
		exit;
	}

	$safeName = str_replace(["\r", "\n"], '', $name);
	$safeEmail = str_replace(["\r", "\n"], '', $email);

	// Kontaktanfrage immer lokal protokollieren.
	$messagesDir = DATA_PATH . '/messages';
	if (!is_dir($messagesDir)) {
		mkdir($messagesDir, 0775, true);
	}
	$entry = sprintf("[%s] %s <%s>\n%s\n----\n", date('c'), $safeName, $safeEmail, $message);
	file_put_contents($messagesDir . '/contact.log', $entry, FILE_APPEND);

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

	unset($_SESSION['form_old'], $_SESSION['form_errors']);
	csrf_token_rotate();

	header('Location: /contact', true, 302);
	exit;
}
