<?php

// Ensure no output is sent before headers
// Expose the "Location" header so it can be read by JavaScript
header("Access-Control-Expose-Headers: Location");

function normalize_spa_path($url)
{
	$path = parse_url((string) $url, PHP_URL_PATH);
	$path = $path !== null ? urldecode($path) : '';
	$path = rtrim((string) $path, '/');
	if ($path === '') {
		$path = '/';
	}
	return strtolower($path);
}

function spa_all_is_on($spa_all_state_file)
{
	if (!file_exists($spa_all_state_file)) {
		file_put_contents($spa_all_state_file, '1');
		return true;
	}

	$state = trim((string) file_get_contents($spa_all_state_file));
	if ($state === '') {
		file_put_contents($spa_all_state_file, '1');
		return true;
	}

	if ($state === '0') {
		return false;
	}

	if ($state !== '1') {
		file_put_contents($spa_all_state_file, '1');
	}

	return true;
}

function turn_on_spa_all($spa_all_state_file, $urls_no_opts)
{
	file_put_contents($spa_all_state_file, '1');
	header("Location: " . $urls_no_opts);
	exit();
}

function turn_off_spa_all($spa_all_state_file, $urls_no_opts)
{
	file_put_contents($spa_all_state_file, '0');
	header("Location: " . $urls_no_opts);
	exit();
}

// Function to turn on SPA for the current URL (remove from exclusion list)
function turn_on_spa($base_url, $urls_cached_file, $urls_no_opts)
{
	$normalized_path = normalize_spa_path($base_url);
	$urls_cached = file_exists($urls_cached_file) ? file($urls_cached_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_cached = array_filter($urls_cached, function ($line) {
		return trim($line) !== '' && !str_starts_with(trim($line), '#');
	});
	$urls_cached = array_filter($urls_cached, function ($url) use ($normalized_path) {
		return normalize_spa_path($url) !== $normalized_path;
	});
	file_put_contents($urls_cached_file, implode(PHP_EOL, $urls_cached) . PHP_EOL);

	// Register a shutdown function
	register_shutdown_function(function () {
		global $shouldRunShutdownFunction;
		if ($shouldRunShutdownFunction) {
			// Your shutdown logic here
			echo "Shutdown function executed.";
		}
	});

	header("Location: " . $urls_no_opts);
	exit();
}

// Function to turn off SPA for the current URL (add to exclusion list)
function turn_off_spa($base_url, $urls_cached_file, $urls_no_opts)
{
	$normalized_path = normalize_spa_path($base_url);
	$urls_cached = file_exists($urls_cached_file) ? file($urls_cached_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_cached = array_filter($urls_cached, function ($line) {
		return trim($line) !== '' && !str_starts_with(trim($line), '#');
	});
	$cached_paths = array_map('normalize_spa_path', $urls_cached);

	if (!in_array($normalized_path, $cached_paths)) {
		$urls_cached[] = $normalized_path;
		file_put_contents($urls_cached_file, implode(PHP_EOL, $urls_cached) . PHP_EOL);
	}

	header("Location: " . $urls_no_opts);
	exit();
}

// Function to check if SPA is on or off for a given URL
function spa_is_on($url)
{
	$normalized_path = normalize_spa_path($url);

	// Path to the urls-no-cached.txt file
	$urls_cached_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-spa.txt';

	// Read and normalize the cached URLs by path only (host agnostic)
	$urls_cached = file_exists($urls_cached_file) ? file($urls_cached_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_cached = array_filter($urls_cached, function ($line) {
		return trim($line) !== '' && !str_starts_with(trim($line), '#');
	});
	$cached_paths = array_map('normalize_spa_path', $urls_cached);

	// SPA is ON by default; OFF only if path is in exclusion list
	return !in_array($normalized_path, $cached_paths);
}

function strip_spa_control_params($url)
{
	$urls_no_opts = str_replace("?turn-off-spa=1", "", $url);
	$urls_no_opts = str_replace("&turn-off-spa=1", "", $urls_no_opts);
	$urls_no_opts = str_replace("?turn-on-spa=1", "", $urls_no_opts);
	$urls_no_opts = str_replace("&turn-on-spa=1", "", $urls_no_opts);
	$urls_no_opts = str_replace("?turn-off-spa-all=1", "", $urls_no_opts);
	$urls_no_opts = str_replace("&turn-off-spa-all=1", "", $urls_no_opts);
	$urls_no_opts = str_replace("?turn-on-spa-all=1", "", $urls_no_opts);
	$urls_no_opts = str_replace("&turn-on-spa-all=1", "", $urls_no_opts);
	return $urls_no_opts;
}

// Function to get the URL without cache control parameters
function get_url_without_spa_params($original_spa_url)
{
	$urls_no_opts = strip_spa_control_params($original_spa_url);

	// Safety: if toggle is triggered directly on /common/spa.php, redirect back to previous page.
	$path = parse_url($urls_no_opts, PHP_URL_PATH);
	if ($path && preg_match('#/common/spa\.php$#i', $path)) {
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		if ($referer !== '') {
			$referer_host = strtolower((string) parse_url($referer, PHP_URL_HOST));
			$current_host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
			$referer_path = (string) parse_url($referer, PHP_URL_PATH);

			if ($referer_host !== '' && $referer_host === $current_host && !preg_match('#/common/spa\.php$#i', $referer_path)) {
				return strip_spa_control_params($referer);
			}
		}

		$scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) == 443)) ? 'https://' : 'http://';
		return $scheme . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
	}

	return $urls_no_opts;
}

// Function to start spa with js
function setup_spa_buffer($html)
{

	// να μην κάνει τίποτα αμα ειναι μέσα στο admin
	if (is_inside_any_admin() or is_iframe()) { //να περνάει το spa
		return $html;
	}

	$spa_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-spa-all.txt';
	$spa_all_enabled = spa_all_is_on($spa_all_state_file);

	//if not exists inside html, attach in spa js inside script in html
	if ($spa_all_enabled && !str_contains($html, 'spa.js')) {
		$html = str_ireplace('</head>', '<script defer src="/common/spa.js"></script></head>', $html);
	}

	// Push up turn off and turn on links after cache because this links is only on admin users
	if (is_user_admin_back_end() && !is_inside_any_admin()) {

		$base_url = get_url_without_spa_params(get_current_url());

		// Current url status
		$put = null;

		// Current url status for
		if (!spa_is_on($base_url)) {

			// If is off
			$url = update_url_params(array('turn-on-spa' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 255, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt;\">Spa: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";

		} else {

			// If is on
			$url = update_url_params(array('turn-off-spa' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 255, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt\">Spa: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		if (!$spa_all_enabled) {
			$url_all = update_url_params(array('turn-on-spa-all' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 255, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt;\">Spa all: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";
		} else {
			$url_all = update_url_params(array('turn-off-spa-all' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 255, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt\">Spa all: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		$html = push_admin_link_on_html($html, $put, str_replace('.php', '', basename(__FILE__)));
		return $html;
	} else {
		return $html;
	}
}

// load functions
include_once __DIR__ . "/functions.php";
if (file_exists(__DIR__ . '/polyfills.php')) {
	include_once __DIR__ . '/polyfills.php';
}

// Normalize the URL to exclude query parameters
$base_url = get_current_url_no_query();

// Handle turn-off and turn-on parameters
$urls_spa_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-spa.txt';
$spa_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-spa-all.txt';

// Get the URL without cache control parameters
$urls_no_opts = get_url_without_spa_params(get_current_url());
if (isset($_GET['turn-off-spa-all'])) {
	turn_off_spa_all($spa_all_state_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-spa-all'])) {
	turn_on_spa_all($spa_all_state_file, $urls_no_opts);
} elseif (isset($_GET['turn-off-spa'])) {
	turn_off_spa($base_url, $urls_spa_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-spa'])) {
	turn_on_spa($base_url, $urls_spa_file, $urls_no_opts);
}

//attach here the spa buffer
ob_start("setup_spa_buffer");
