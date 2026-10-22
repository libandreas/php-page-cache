<?php

if (!defined('TTFB_CLS_PLUGIN_URL')) {
	define('TTFB_CLS_PLUGIN_URL', $_SERVER['DOCUMENT_ROOT']);
}

// Function to generate a cache key based on the user-select:none;URL
function get_cache_file_from_url($original_cached_url)
{

	// Generate the cache key with the appropriate extension
	return $_SERVER['DOCUMENT_ROOT'] . '/cache/' . md5($original_cached_url) . '.html';
}

// Function to check if caching is on or off for a given URL (uses only path, ignoring host and protocol)
function ttfb_is_on($url)
{
	// Check if the URL is null
	if ($url == null) {
		return true;
	}

	// Extract only the path from the URL (no protocol, no host)
	$url_path = parse_url($url, PHP_URL_PATH);

	// Remove trailing slash for consistent comparison
	$url_path = rtrim($url_path, '/');
	if (empty($url_path)) {
		$url_path = '/';
	}

	// Path to the urls-cache.txt file
	$urls_excluded_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cache.txt';

	// Read excluded URLs
	$urls_excluded = file_exists($urls_excluded_file) ? file($urls_excluded_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

	// Filter out comments and empty lines
	$urls_excluded = array_filter($urls_excluded, function ($line) {
		return trim($line) !== '' && !str_starts_with(trim($line), '#');
	});

	// Extract only paths from excluded URLs (ignore protocol and host)
	$excluded_paths = array_map(function ($url) {
		$path = parse_url($url, PHP_URL_PATH);
		$path = rtrim($path, '/');
		if (empty($path)) {
			$path = '/';
		}
		return strtolower($path);
	}, $urls_excluded);

	// Check if current path is in exclusion list (case-insensitive)
	return !in_array(strtolower($url_path), $excluded_paths);
}

// Function to check if global cache-all is enabled
function ttfb_all_is_on($cache_all_state_file)
{
	if (!file_exists($cache_all_state_file)) {
		file_put_contents($cache_all_state_file, '1');
		return true;
	}

	$state = trim((string) file_get_contents($cache_all_state_file));
	if ($state === '') {
		file_put_contents($cache_all_state_file, '1');
		return true;
	}

	// OFF only if exactly 0, otherwise ON
	if ($state === '0') {
		return false;
	}

	if ($state !== '1') {
		file_put_contents($cache_all_state_file, '1');
	}

	return true;
}

// Function to turn on cache for all URLs
function turn_on_ttfb_all($cache_all_state_file, $urls_no_opts)
{
	file_put_contents($cache_all_state_file, '1');
	header("Location: " . $urls_no_opts);
	exit();
}

// Function to turn off cache for all URLs
function turn_off_ttfb_all($cache_all_state_file, $urls_no_opts)
{
	file_put_contents($cache_all_state_file, '0');
	header("Location: " . $urls_no_opts);
	exit();
}

// Function to handle output buffering and cache the content
function setup_ttfb_output($buffer)
{
	$url = get_current_url();

	// Create the cache directory if it doesn't exist
	if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/cache/')) {
		mkdir($_SERVER['DOCUMENT_ROOT'] . '/cache/', 0755, true);
	}

	// Write the content to the cache file
	$cache_file = get_cache_file_from_url($url);
	$buffer = trim($buffer);

	// Push the js version in the html cache
	if (!str_contains($buffer, 'ttfb.js')) {
		$buffer = str_ireplace('</head>', '<script defer src="' . TTFB_CLS_PLUGIN_URL . 'common/ttfb.js"></script></head>', $buffer);
	} else {
		$buffer = str_ireplace('</head>', '<script defer src="/common/ttfb.js"></script></head>', $buffer);
	}

	file_put_contents($cache_file, $buffer);
	return $buffer;
}

// Puts cache links
function setup_ttfb_links($html)
{
	$base_url = get_url_without_cache_params(get_current_url());
	if ($html == null) {
		return $html; //we put this for cls dont remove
	}

	// Push up turn off and turn on links after cache because this links is only on admin users
	if (is_user_admin_back_end() && !is_inside_any_admin() && !is_iframe()) {

		// Get current url with params now original url please
		$put = null;
		if (!ttfb_is_on($base_url)) {

			// If is off
			$url = update_url_params(array('turn-on-ttfb' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"text-decoration:none;color:black;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt\">Cache: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";

		} else {

			// If is on
			$url = update_url_params(array('turn-off-ttfb' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"text-decoration:none;color:black;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt\">Cache: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		$cache_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cache-all.txt';
		if (!ttfb_all_is_on($cache_all_state_file)) {
			$url_all = update_url_params(array('turn-on-ttfb-all' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"text-decoration:none;color:black;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt\">Cache all: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";
		} else {
			$url_all = update_url_params(array('turn-off-ttfb-all' => true), false, false, $base_url);
			$put .= "<a class=\"cms-link\" style=\"text-decoration:none;color:black;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt\">Cache all: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		$html = push_admin_link_on_html($html, $put, str_replace('.php', '', basename(__FILE__)));
		return $html;
	} else {

		return $html;
	}
}

// Function to turn on caching for the current URL (remove from exclusion list)
function turn_on_ttfb($base_url, $urls_excluded_file, $urls_no_opts)
{
	// Normalize the URL
	$normalized_url = normalize_url_2($base_url);

	// Read existing excluded URLs
	$urls_excluded = file_exists($urls_excluded_file) ? file($urls_excluded_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_excluded = array_map('normalize_url_2', $urls_excluded);

	// Filter out comments and empty lines
	$urls_excluded = array_filter($urls_excluded, function ($line) {
		return trim($line) !== '' && !str_starts_with(trim($line), '#');
	});

	// Remove URL from exclusion list to enable caching
	$urls_excluded = array_filter($urls_excluded, function ($url) use ($normalized_url) {
		return $url !== $normalized_url;
	});
	file_put_contents($urls_excluded_file, implode(PHP_EOL, $urls_excluded) . PHP_EOL);

	header("Location: " . $urls_no_opts);
	exit();
}

// Function to turn off caching for the current URL (add to exclusion list)
function turn_off_ttfb($base_url, $urls_excluded_file, $urls_no_opts)
{
	// Normalize the URL
	$normalized_url = normalize_url_2($base_url);

	// Read existing excluded URLs
	$urls_excluded = file_exists($urls_excluded_file) ? file($urls_excluded_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_excluded = array_map('normalize_url_2', $urls_excluded);

	// Filter out comments and empty lines
	$urls_excluded = array_filter($urls_excluded, function ($line) {
		return trim($line) !== '' && !str_starts_with(trim($line), '#');
	});

	// Add URL to exclusion list to disable caching
	if (!in_array($normalized_url, $urls_excluded)) {
		$urls_excluded[] = $normalized_url;
		file_put_contents($urls_excluded_file, implode(PHP_EOL, $urls_excluded) . PHP_EOL);
	}

	header("Location: " . $urls_no_opts);
	exit();
}

// Function to refresh cache for the current URL
function refresh_cache($url)
{
	$cache_file_path = get_cache_file_from_url($url);
	if (file_exists($cache_file_path)) {
		unlink($cache_file_path);
	}

	header("Location: " . $url);
	exit();
}

function strip_ttfb_control_params($url)
{
	$url = str_replace("?turn-off-ttfb=1", "", $url);
	$url = str_replace("&turn-off-ttfb=1", "", $url);
	$url = str_replace("?turn-on-ttfb=1", "", $url);
	$url = str_replace("&turn-on-ttfb=1", "", $url);
	$url = str_replace("?turn-off-ttfb-all=1", "", $url);
	$url = str_replace("&turn-off-ttfb-all=1", "", $url);
	$url = str_replace("?turn-on-ttfb-all=1", "", $url);
	$url = str_replace("&turn-on-ttfb-all=1", "", $url);
	$url = str_replace("?refresh-ttfb=1", "", $url);
	$url = str_replace("&refresh-ttfb=1", "", $url);
	$url = str_replace("?refresh-ttfb-all=1", "", $url);
	$url = str_replace("&refresh-ttfb-all=1", "", $url);
	return $url;
}

// Function to get the URL without cache control parameters
function get_url_without_cache_params($url)
{
	$url = strip_ttfb_control_params($url);

	// Safety: if toggle is triggered directly on /common/ttfb.php, redirect back to previous page.
	$path = parse_url($url, PHP_URL_PATH);
	if ($path && preg_match('#/common/ttfb\.php$#i', $path)) {
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		if ($referer !== '') {
			$referer_host = strtolower((string) parse_url($referer, PHP_URL_HOST));
			$current_host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
			$referer_path = (string) parse_url($referer, PHP_URL_PATH);

			if ($referer_host !== '' && $referer_host === $current_host && !preg_match('#/common/ttfb\.php$#i', $referer_path)) {
				return strip_ttfb_control_params($referer);
			}
		}

		$scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) == 443)) ? 'https://' : 'http://';
		return $scheme . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
	}

	return $url;
}

function is_time_to_refresh_cache_for_that_url($url)
{
	$file_for_time = get_cache_file_from_url($url);
	$current_time = time();

	// Read last refresh time
	if (file_exists($file_for_time)) {
		$last_refresh = (int) filemtime($file_for_time);
	} else {
		$last_refresh = 0;
	}

	$reset_file_path = $_SERVER['DOCUMENT_ROOT'] . '/urls-reset-ttfb.txt';
	$default_reset_time = 172800; // 48 hours in seconds

	if (!file_exists($reset_file_path)) {
		file_put_contents($reset_file_path, $default_reset_time);
		$reset_time = $default_reset_time;
	} else {
		$raw_reset_time = trim((string) file_get_contents($reset_file_path));
		if ($raw_reset_time !== '' && is_numeric($raw_reset_time) && (int) $raw_reset_time > 0) {
			$reset_time = (int) $raw_reset_time;
		} else {
			file_put_contents($reset_file_path, $default_reset_time);
			$reset_time = $default_reset_time;
		}
	}

	// Check if reset time passed
	if (($current_time - $last_refresh) > $reset_time) {
		return true;
	}

	return false;
}

require_once __DIR__ . '/functions.php';
if (file_exists(__DIR__ . '/polyfills.php')) {
	require_once __DIR__ . '/polyfills.php';
}

// Skip static file types
if (is_static_type($_SERVER['REQUEST_URI'])) {
	return;
}

// If is inside any admin
if (is_inside_any_admin() or is_iframe()) {
	return;
}

// Main script logic
$url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Remove cache control parameters
$urls_no_opts = get_url_without_cache_params($url);

// Handle turn-off-ttfb-ttfb and turn-on-ttfb parameters
$urls_ttfb_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cache.txt';
$cache_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cache-all.txt';

// Handle all-switch first to avoid conflicts when old links accidentally include both params
if (isset($_GET['turn-off-ttfb-all'])) {
	turn_off_ttfb_all($cache_all_state_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-ttfb-all'])) {
	turn_on_ttfb_all($cache_all_state_file, $urls_no_opts);
} elseif (isset($_GET['turn-off-ttfb'])) {
	turn_off_ttfb($urls_no_opts, $urls_ttfb_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-ttfb'])) {
	turn_on_ttfb($urls_no_opts, $urls_ttfb_file, $urls_no_opts);
}

// Check for cached content
$cache_file = get_cache_file_from_url($urls_no_opts);

// Check if caching is turned off for the current URL
$caching_enabled = ttfb_is_on($urls_no_opts);
$cache_all_enabled = ttfb_all_is_on($cache_all_state_file);
$caching_enabled = ($caching_enabled && $cache_all_enabled);

// Cache exists
$caching_exists = file_exists($cache_file);

// Check if is time to refresh cache for that url
$cache_is_time_to_refresh = is_time_to_refresh_cache_for_that_url($urls_no_opts);

//if show admin links is true from the ttfb.js
if ($caching_exists == true && $cache_is_time_to_refresh == false && $caching_enabled == true && $_SERVER['REQUEST_METHOD'] != 'POST') {

	// Serve cached content
	// Dont flush here because will break the reading file
	readfile($cache_file);
	exit();
} elseif ($caching_enabled == true && $_SERVER['REQUEST_METHOD'] != 'POST') {

	// Reset all cookies to make anonymous
	$_COOKIE = array();
	$_SESSION = array();
	ob_start('setup_ttfb_output'); //this run on the result of the cls output!!

	// Run Cls here is exists
	// $cls_plugin_file = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/intelligent-layout-shift-patch/common/cls.php';
	// $cls_common_file = $_SERVER['DOCUMENT_ROOT'] . '/common/cls.php';
	// if (file_exists($cls_plugin_file) or file_exists($cls_common_file) && function_exists('setup_cls_frames')) {
	// 	ob_start('setup_cls_frames');
	// }
} else {

	// Cancel the cache generation and fall in to default mode
	ob_start('setup_ttfb_links');

	// Run Cls here is exists
	// $cls_plugin_file = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/intelligent-layout-shift-patch/common/cls.php';
	// $cls_common_file = $_SERVER['DOCUMENT_ROOT'] . '/common/cls.php';
	// if (file_exists($cls_plugin_file) or file_exists($cls_common_file) && function_exists('setup_cls_frames')) {
	// 	ob_start('setup_cls_frames');
	// }
}
