<?php

if (!defined('TTFB_CLS_PLUGIN_URL')) {
	define('TTFB_CLS_PLUGIN_URL', $_SERVER['DOCUMENT_ROOT']);
}

// Function to turn on CLS for the current URL (remove from exclusion list)
function turn_on_cls($base_url, $urls_excluded_file, $urls_no_opts)
{
	$normalized_url = normalize_url_2($base_url);
	$urls_excluded = file_exists($urls_excluded_file) ? file($urls_excluded_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_excluded = array_map('normalize_url_2', $urls_excluded);

	// Remove URL from exclusion list to enable CLS
	$urls_excluded = array_filter($urls_excluded, function ($url) use ($normalized_url) {
		return $url !== $normalized_url;
	});
	file_put_contents($urls_excluded_file, implode(PHP_EOL, $urls_excluded) . PHP_EOL);

	header("Location: " . $urls_no_opts);
	exit();
}

// Function to turn off CLS for the current URL (add to exclusion list)
function turn_off_cls($base_url, $urls_excluded_file, $urls_no_opts)
{
	$normalized_url = normalize_url_2($base_url);
	$urls_excluded = file_exists($urls_excluded_file) ? file($urls_excluded_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_excluded = array_map('normalize_url_2', $urls_excluded);

	// Add URL to exclusion list to disable CLS
	if (!in_array($normalized_url, $urls_excluded)) {
		$urls_excluded[] = $normalized_url;
		file_put_contents($urls_excluded_file, implode(PHP_EOL, $urls_excluded) . PHP_EOL);
	}

	header("Location: " . $urls_no_opts);
	exit();
}

// Function to check if CLS is enabled for a given URL (uses only path, ignoring host and protocol)
function cls_is_on($url)
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

	// Path to the cls-exclusions.txt file
	$urls_excluded_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cls.txt';

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

// Function to check if global CLS all is enabled
function cls_all_is_on($cls_all_state_file)
{
	if (!file_exists($cls_all_state_file)) {
		file_put_contents($cls_all_state_file, '1');
		return true;
	}

	$state = trim((string) file_get_contents($cls_all_state_file));
	if ($state === '') {
		file_put_contents($cls_all_state_file, '1');
		return true;
	}

	// OFF only if exactly 0, otherwise ON
	if ($state === '0') {
		return false;
	}

	if ($state !== '1') {
		file_put_contents($cls_all_state_file, '1');
	}

	return true;
}

// Function to turn on CLS for all URLs
function turn_on_cls_all($cls_all_state_file, $urls_no_opts)
{
	file_put_contents($cls_all_state_file, '1');
	header("Location: " . $urls_no_opts);
	exit();
}

// Function to turn off CLS for all URLs
function turn_off_cls_all($cls_all_state_file, $urls_no_opts)
{
	file_put_contents($cls_all_state_file, '0');
	header("Location: " . $urls_no_opts);
	exit();
}

// Function to get the URL without cache control parameters
function strip_cls_control_params($url)
{
	$url = str_replace("?turn-off-cls=1", "", $url);
	$url = str_replace("&turn-off-cls=1", "", $url);
	$url = str_replace("?turn-on-cls=1", "", $url);
	$url = str_replace("&turn-on-cls=1", "", $url);
	$url = str_replace("?turn-off-cls-all=1", "", $url);
	$url = str_replace("&turn-off-cls-all=1", "", $url);
	$url = str_replace("?turn-on-cls-all=1", "", $url);
	$url = str_replace("&turn-on-cls-all=1", "", $url);
	return $url;
}

// Function to get the URL without cache control parameters
function get_url_without_cls_params($original_cls_url)
{
	$urls_no_opts = strip_cls_control_params($original_cls_url);

	// Safety: if toggle is triggered directly on /common/cls.php, redirect back to the previous page.
	$path = parse_url($urls_no_opts, PHP_URL_PATH);
	if ($path && preg_match('#/common/cls\.php$#i', $path)) {
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		if ($referer !== '') {
			$referer_host = strtolower((string) parse_url($referer, PHP_URL_HOST));
			$current_host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
			$referer_path = (string) parse_url($referer, PHP_URL_PATH);

			if ($referer_host !== '' && $referer_host === $current_host && !preg_match('#/common/cls\.php$#i', $referer_path)) {
				return strip_cls_control_params($referer);
			}
		}

		$scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? null) == 443)) ? 'https://' : 'http://';
		return $scheme . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
	}

	return $urls_no_opts;
}

// Function to get cached content
function check_cls_file($cls_file)
{
	$file_path = $_SERVER['DOCUMENT_ROOT'] . '/urls-reset-cls.txt';
	$default_cls_reset = 604800; // 7 day in seconds

	if (!file_exists($file_path)) {
		file_put_contents($file_path, $default_cls_reset);
		$cls_reset = $default_cls_reset;
	} else {
		$value = trim((string) file_get_contents($file_path));
		if ($value !== '' && is_numeric($value) && (int) $value > 0) {
			$cls_reset = (int) $value;
		} else {
			file_put_contents($file_path, $default_cls_reset);
			$cls_reset = $default_cls_reset;
		}
	}

	if (file_exists($cls_file) && (time() - filemtime($cls_file)) < $cls_reset) {
		return $cls_file;
	}

	return false;
}

function output_flying_thumbnail($src, $width, $height)
{
	// Define the cache directory
	$cls_dir = $_SERVER['DOCUMENT_ROOT'] . '/cache/';

	// Create the cache directory if it doesn't exist
	if (!is_dir($cls_dir)) {
		mkdir($cls_dir, 0755, true);
	}

	// Generate a unique cache file name based on the source path and dimensions
	$image_info = getimagesize($src);
	if ($image_info === false) {
		return false;
	}

	$image_type = $image_info[2];
	$extension = image_type_to_extension($image_type, false);
	$cls_file = $cls_dir . md5($src . $width . $height) . '.' . $extension;

	// Check if the cached file exists and is still valid
	if (file_exists($cls_file) && check_cls_file($cls_file)) {
		header('Content-Type: ' . image_type_to_mime_type($image_type));
		readfile($cls_file);
		return true;
	}

	// Sanitize the input path
	$src = realpath($src);
	if (!$src || !file_exists($src)) {
		return false;
	}

	$image_info = getimagesize($src);
	if (!$image_info) {
		return false;
	}

	list($original_width, $original_height) = $image_info;
	$image_type = $image_info[2];

	// Validate image type
	$valid_image_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
	if (!in_array($image_type, $valid_image_types)) {
		return false;
	}

	// Create the source image based on the image type
	switch ($image_type) {
		case IMAGETYPE_JPEG:
			$source = imagecreatefromjpeg($src);
			break;
		case IMAGETYPE_PNG:
			$source = imagecreatefrompng($src);
			break;
		case IMAGETYPE_GIF:
			$source = imagecreatefromgif($src);
			break;
		case IMAGETYPE_WEBP:
			if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
				return false;
			}
			$source = imagecreatefromwebp($src);
			break;
		default:
			return false;
	}

	// Calculate width or height aspect ratio - fixes
	if ($height > $width) {
		$width = (int) (($original_width / $original_height) * $height);
	} else {
		$height = (int) (($original_height / $original_width) * $width);
	}

	// Create a new true color image with the specified width and height
	$thumbnail = imagecreatetruecolor($width, $height);

	// Preserve transparency for PNG and WEBP images
	if ($image_type == IMAGETYPE_PNG || $image_type == IMAGETYPE_WEBP) {
		// Turn off alpha blending and set alpha flag
		imagealphablending($thumbnail, false);
		imagesavealpha($thumbnail, true);

		// Allocate a transparent color
		$transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
		imagefill($thumbnail, 0, 0, $transparent);
	}

	// Resize the image
	imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $width, $height, $original_width, $original_height);

	// Save the thumbnail to the cache directory
	switch ($image_type) {
		case IMAGETYPE_JPEG:
			imagejpeg($thumbnail, $cls_file, 80);
			header('Content-Type: image/jpeg');
			imagejpeg($thumbnail);
			break;
		case IMAGETYPE_PNG:
			imagepng($thumbnail, $cls_file, 0);
			header('Content-Type: image/png');
			imagepng($thumbnail);
			break;
		case IMAGETYPE_GIF:
			imagegif($thumbnail, $cls_file);
			header('Content-Type: image/gif');
			imagegif($thumbnail);
			break;
		case IMAGETYPE_WEBP:
			imagewebp($thumbnail, $cls_file, 80);
			header('Content-Type: image/webp');
			imagewebp($thumbnail, null, 80);
			break;
	}

	// Free up memory
	imagedestroy($thumbnail);
	imagedestroy($source);
	return true;
}

// Function to replace image src attributes with a transparent placeholder containing actual dimensions
function setup_cls_frames($html)
{

	// να μην κάνει τίποτα αμα ειναι μέσα στο admin
	if (is_inside_any_admin() or is_iframe()) {
		return $html;
	}

	$cls_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cls-all.txt';
	$cls_enabled = cls_is_on(get_current_url()) && cls_all_is_on($cls_all_state_file);

	if ($cls_enabled) {

		// Updated pattern to exclude base64 images and SVG images
		$pattern = '/<img\s+([^>]*?)src=["\'](?!data:image\/)([^"\']+)["\']([^>]*?)>/i';
		$matches = [];
		preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			// Skip SVG images entirely
			$src = $match[2];
			if (preg_match('/\.svg(\?|#|$)/i', $src)) {
				continue;
			}

			$modifiedImgTag = replace_image_tag($match);

			//if function was successful, replace the original image tag with the modified one
			if ($modifiedImgTag !== false && $modifiedImgTag !== '') {

				$html = str_replace($match[0], $modifiedImgTag, $html);
			}
		}

		// Push the js version in the html cache
		if (defined('ABSPATH') && !str_contains($html, 'cls.js')) {
			$html = str_ireplace('</head>', '<script defer src="' . TTFB_CLS_PLUGIN_URL . 'common/cls.js"></script></head>', $html);
		} else {
			$html = str_ireplace('</head>', '<script defer src="/common/cls.js"></script></head>', $html);
		}
	}

	// Push up turn off and turn on links after cache because this links is only on admin users
	if (is_user_admin_back_end() && !is_inside_any_admin()) {

		// Current url status
		$put = null;

		// Current url status for
		if (!cls_is_on(get_current_url())) {

			// If is off
			$url = update_url_params(array('turn-on-cls' => true), false, false, get_current_url());
			$put .= "<a class=\"cms-link\" style=\"background:rgba(173, 216, 230, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt;\">Layout fix: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";

		} else {

			// If is on
			$url = update_url_params(array('turn-off-cls' => true), false, false, get_current_url());
			$put .= "<a class=\"cms-link\" style=\"background:rgba(173, 216, 230, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt\">Layout fix: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		if (!cls_all_is_on($cls_all_state_file)) {

			// If all is off
			$url_all = update_url_params(array('turn-on-cls-all' => true), false, false, get_current_url());
			$put .= "<a class=\"cms-link\" style=\"background:rgba(173, 216, 230, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt;\">Layout fix all: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";

		} else {

			// If all is on
			$url_all = update_url_params(array('turn-off-cls-all' => true), false, false, get_current_url());
			$put .= "<a class=\"cms-link\" style=\"background:rgba(173, 216, 230, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt\">Layout fix all: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		$html = push_admin_link_on_html($html, $put, str_replace('.php', '', basename(__FILE__)));
		return $html;
	} else {
		return $html;
	}
}

// Function to replace the image tag
function replace_image_tag($match)
{
	$attributesBeforeSrc = $match[1];
	$src = $match[2];
	$src_original = $match[2];
	$attributesAfterSrc = $match[3];

	$src_trim = trim($src);
	$parsed_src = parse_url($src_trim);
	$src_path = $parsed_src['path'] ?? '';
	$src_path_lower = strtolower($src_path);
	if ($src_path_lower !== '' && substr($src_path_lower, -4) === '.svg') {
		return false;
	}

	// Get image dimensions
	$base = get_current_url();
	if (!isset($parsed_src['scheme']) && !str_starts_with($src_trim, '//')) {

		$src = rel2abs($src, $base); // Inline code replacing url2path
	}

	// Get image dimensions
	$src_for_dems = $_SERVER['DOCUMENT_ROOT'] . parse_url($src, PHP_URL_PATH); // Inline code replacing url2path

	// Check if the image exists
	if (file_exists($src_for_dems)) {

		$dimensions = get_image_dimensions($src_for_dems);
		$width = $dimensions['width'];
		$height = $dimensions['height'];

		// Create a transparent placeholder with the actual dimensions
		$placeholder = create_placeholder($width, $height);

		// Return the modified img tag with width and height attributes (METHOD 2 + 3)
		// Method 2: Width/Height attributes prevent layout shift
		// Method 3: SVG placeholder provides instant visual feedback
		return '<img ' . $attributesBeforeSrc . 'src="' . $placeholder . '" width="' . $width . '" height="' . $height . '" data-original-src="' . $src_original . '"' . $attributesAfterSrc . '>';
	} else {

		return false;
	}
}

// Function to get image dimensions
function get_image_dimensions($src)
{

	// Get image dimensions
	$dimensions = getimagesize($src);
	if ($dimensions) {
		return ['width' => $dimensions[0], 'height' => $dimensions[1]];
	} else {

		// Default dimensions if unable to get the actual dimensions
		return ['width' => 1, 'height' => 1];
	}
}

// Function to create a transparent placeholder with the given dimensions
function create_placeholder($width, $height)
{
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '"></svg>';
	return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

require_once __DIR__ . '/functions.php';
if (file_exists(__DIR__ . '/polyfills.php')) {
	require_once __DIR__ . '/polyfills.php';
}

// Skip static file types
if (is_static_type($_SERVER['REQUEST_URI']) && !isset($_GET['get-thumb'])) {
	return;
}

// If is inside any admin
if ((is_inside_any_admin() or is_iframe()) && !isset($_GET['get-thumb'])) {
	return;
}

// Main script logic
if (isset($_GET['get-thumb']) && isset($_GET['src']) && isset($_GET['width']) && isset($_GET['height'])) {

	// Get image thumbnail
	$src = urldecode($_GET['src']);
	$src_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($src, PHP_URL_PATH);
	$width = intval($_GET['width']);
	$height = intval($_GET['height']);

	if (!file_exists($src_path)) {

		// Not found
		http_response_code(404);
		echo 'Image not found: ' . $src_path;
		exit();
	} else {
		$result = output_flying_thumbnail($src_path, $width, $height);

		if ($result) {
			// Success
			exit();
		} else {
			// Unsupported Media Type
			http_response_code(415);
			echo 'Image is unsupported image type';
			exit();
		}
	}
}

// Main script logic
$url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Remove cache control parameters
$urls_no_opts = get_url_without_cls_params($url);

// Handle turn-off and turn-on parameters
$urls_cls_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cls.txt';
$cls_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-cls-all.txt';

// Get the URL without cache control parameters
if (isset($_GET['turn-off-cls'])) {
	turn_off_cls($urls_no_opts, $urls_cls_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-cls'])) {
	turn_on_cls($urls_no_opts, $urls_cls_file, $urls_no_opts);
} elseif (isset($_GET['turn-off-cls-all'])) {
	turn_off_cls_all($cls_all_state_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-cls-all'])) {
	turn_on_cls_all($cls_all_state_file, $urls_no_opts);
}

ob_start('setup_cls_frames');
return;
