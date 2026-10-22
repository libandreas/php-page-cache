<?php

// How to use element caching:
// 1. Add 'cache-element-n' class to HTML elements you want to cache
// 2. System automatically caches content for faster loading
// 3. Use admin interface to manage caching per URL
//
// Example:
// <div class="cache-element-1">Content</div>
//
// Note: Caching is URL-specific, stores HTML in cache files

// Check if caching is enabled for a URL
function cache_is_on_for_elements($url)
{
	// Check if the URL is null
	if ($url == null)
		return false;

	// Normalize the URL to exclude query parameters
	$url = strtok($url, '?');
	$normalized_url = normalize_url($url);
	$normalized_url = patch_url_allways_to_production($normalized_url);

	// Path to the cached URLs file
	$urls_cached_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-elements-cache.txt';

	// Read and normalize the cached URLs
	$urls_cached = file_exists($urls_cached_file) ? file($urls_cached_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_cached = array_map('normalize_url', $urls_cached);
	$urls_cached = array_map('patch_url_allways_to_production', $urls_cached);

	// Check if the normalized URL is in the cached URLs
	return in_array($normalized_url, $urls_cached);
}

// Puts cache links
function setup_cache_elements_links($html)
{
	$url = get_current_url();

	// Push up turn off and turn on links after cache because this links is only on admin users
	if (is_user_admin_back_end() && !is_inside_any_admin()) {

		// Current url status
		$put = null;

		// Current url status for
		if (!cache_is_on_for_elements($url)) {

			// If is off
			$url = update_url_params(array('turn-on-elements' => true), false, false, $url);
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 0, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt\">Cache elements: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";

		} else {

			// If is on
			$url = update_url_params(array('turn-off-elements' => true), false, false, $url);
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 0, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url\"><small style=\"font-size:12pt\">Cache elements: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		// Global cache elements all status (button under per-url button)
		$cache_elements_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-elements-cache-all.txt';
		if (!cache_elements_all_is_on($cache_elements_all_state_file)) {

			// If global is off
			$url_all = update_url_params(array('turn-on-elements-all' => true), false, false, get_current_url());
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 0, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt\">Cache elements all: <span>ON</span> / <strong style=\"color:red\">OFF</strong></small></a><br>";
		} else {

			// If global is on
			$url_all = update_url_params(array('turn-off-elements-all' => true), false, false, get_current_url());
			$put .= "<a class=\"cms-link\" style=\"background:rgba(255, 255, 0, 0.5);text-decoration:none;color:darkblue;text-shadow: 1px 1px white;\" href=\"$url_all\"><small style=\"font-size:12pt\">Cache elements all: <strong style=\"color:forestgreen\">ON</strong> / <span>OFF</span></small></a><br>";
		}

		$html = push_admin_link_on_html($html, $put, str_replace('.php', '', basename(__FILE__)));
		return $html;
	} else {
		return $html;
	}
}

function setup_cache_elements_output($html)
{
	$base_url = get_current_url_no_query();

	// Skip wp common and static files
	if (strpos(((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '/wp-') !== false || strpos(((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '/common') !== false) {
		return $html;
	}

	// Record the page with the cached elements
	if (!is_dir(dirname(dirname(__FILE__)) . '/cache/')) {
		mkdir(dirname(dirname(__FILE__)) . '/cache/', 0755, true);
	}
	$cache_file = generate_cache_elements_file($base_url);
	$html = trim($html);

	file_put_contents($cache_file, $html);
	return $html;
}

// Function to check if cache is on for caching elements
function turn_on_cache_elements($base_url, $urls_cached_file, $urls_no_opts)
{
	// Normalize the URL
	$base_url = strtok($base_url, '?');
	$normalized_url = normalize_url($base_url);
	$normalized_url = patch_url_allways_to_production($normalized_url);
	$urls_cached = file_exists($urls_cached_file) ? file($urls_cached_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_cached = array_map('normalize_url', $urls_cached);
	$urls_cached = array_map('patch_url_allways_to_production', $urls_cached);

	if (!in_array($normalized_url, $urls_cached)) {
		$urls_cached[] = $normalized_url;
		file_put_contents($urls_cached_file, implode(PHP_EOL, $urls_cached) . PHP_EOL);
	}

	header("Location: " . $urls_no_opts);
	exit();
}

function turn_off_cache_elements($base_url, $urls_cached_file, $urls_no_opts)
{
	// Normalize the URL
	$base_url = strtok($base_url, '?');
	$normalized_url = normalize_url($base_url);
	$normalized_url = patch_url_allways_to_production($normalized_url);
	$urls_cached = file_exists($urls_cached_file) ? file($urls_cached_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	$urls_cached = array_map('normalize_url', $urls_cached);
	$urls_cached = array_map('patch_url_allways_to_production', $urls_cached);

	$urls_cached = array_filter($urls_cached, function ($url) use ($normalized_url) {
		return $url !== $normalized_url;
	});
	file_put_contents($urls_cached_file, implode(PHP_EOL, $urls_cached) . PHP_EOL);

	header("Location: " . $urls_no_opts);
	exit();
}

function cache_elements_all_is_on($cache_elements_all_state_file)
{
	if (!file_exists($cache_elements_all_state_file)) {
		file_put_contents($cache_elements_all_state_file, '1');
		return true;
	}

	$state = trim((string) file_get_contents($cache_elements_all_state_file));
	if ($state === '') {
		file_put_contents($cache_elements_all_state_file, '1');
		return true;
	}

	// OFF only if exactly 0, otherwise ON
	if ($state === '0') {
		return false;
	}

	if ($state !== '1') {
		file_put_contents($cache_elements_all_state_file, '1');
	}

	return true;
}

function turn_on_cache_elements_all($cache_elements_all_state_file, $urls_no_opts)
{
	file_put_contents($cache_elements_all_state_file, '1');
	header("Location: " . $urls_no_opts);
	exit();
}

function turn_off_cache_elements_all($cache_elements_all_state_file, $urls_no_opts)
{
	file_put_contents($cache_elements_all_state_file, '0');
	header("Location: " . $urls_no_opts);
	exit();
}

function generate_cache_elements_file($url)
{
	// Normalize the URL
	$url = strtok($url, '?');
	$normalized_url = normalize_url($url);
	$normalized_url = patch_url_allways_to_production($normalized_url);

	// Generate the cache key with the appropriate extension
	return dirname(dirname(__FILE__)) . '/cache/' . md5($normalized_url) . '.shtml';
}

function update_elem_by_class_php($html, $className, $newContent)
{
	if (strpos($html, '<?php') !== false || strpos($html, '<?=') !== false) {
		$offset = 0;
		$result = '';
		$htmlLength = strlen($html);
		$classPattern = '/<(?<tag>\w+)([^>]*)\bclass=["\']([^"\']*\b' . preg_quote($className, '/') . '\b[^"\']*)["\']([^>]*)>/i';

		while ($offset < $htmlLength && ($startPos = stripos($html, '<', $offset)) !== false) {
			// Check if the tag at $startPos matches the class we're looking for
			if (preg_match($classPattern, $html, $matches, PREG_OFFSET_CAPTURE, $startPos)) {
				$tagStart = $matches[0][1];

				if ($tagStart > $offset) {
					// Append any text before the matched tag
					$result .= substr($html, $offset, $tagStart - $offset);
				}

				$tagName = $matches['tag'][0];
				$fullTag = $matches[0][0];
				$openTagEnd = $tagStart + strlen($fullTag);

				// Find the position of the corresponding closing tag
				$closeTagPos = findClosingTagPosition($html, $tagName, $openTagEnd);
				if ($closeTagPos === false) {
					// Closing tag not found, append the rest and break
					$result .= substr($html, $tagStart);
					break;
				}

				// Append the opening tag
				$result .= substr($html, $tagStart, $openTagEnd - $tagStart);
				// Append the new content
				$result .= $newContent;
				// Append the closing tag
				$closeTagEnd = $closeTagPos + strlen('</' . $tagName . '>');
				$result .= substr($html, $closeTagPos, $closeTagEnd - $closeTagPos);

				// Update the offset
				$offset = $closeTagEnd;
			} else {
				// No matching tag found, append the rest and break
				$result .= substr($html, $offset);
				break;
			}
		}

		// Append any remaining HTML after the last tag
		if ($offset < $htmlLength) {
			//$result .= substr($html, $offset); //this line was causing issues with double html
		}

		return $result;
	} else {
		return $html;
	}
}

function findClosingTagPosition($html, $tagName, $offset)
{
	$openTagPattern = '/<\s*' . preg_quote($tagName, '/') . '\b[^>]*>/i';
	$closeTagPattern = '/<\s*\/\s*' . preg_quote($tagName, '/') . '\s*>/i';
	$htmlLength = strlen($html);
	$position = $offset;
	$depth = 1;

	while ($position < $htmlLength) {
		$nextOpenTag = preg_match($openTagPattern, $html, $matchOpen, PREG_OFFSET_CAPTURE, $position);
		$nextCloseTag = preg_match($closeTagPattern, $html, $matchClose, PREG_OFFSET_CAPTURE, $position);

		$nextOpenTagPos = $nextOpenTag ? $matchOpen[0][1] : false;
		$nextCloseTagPos = $nextCloseTag ? $matchClose[0][1] : false;

		if ($nextCloseTagPos === false) {
			// No closing tag found
			return false;
		}

		if ($nextOpenTagPos !== false && $nextOpenTagPos < $nextCloseTagPos) {
			// Found a nested opening tag
			$depth++;
			$position = $nextOpenTagPos + strlen($matchOpen[0][0]);
		} else {
			// Found a closing tag
			$depth--;
			if ($depth === 0) {
				// Found the matching closing tag
				return $nextCloseTagPos;
			}
			$position = $nextCloseTagPos + strlen($matchClose[0][0]);
		}
	}

	return false; // No matching closing tag found
}

function patch_cached_elements($html_from_cache, $html_from_real_file)
{

	//get cached elements
	$element_1 = read_elem_by_class($html_from_cache, "cache-element-1");
	$element_2 = read_elem_by_class($html_from_cache, "cache-element-2");
	$element_3 = read_elem_by_class($html_from_cache, "cache-element-3");
	$element_4 = read_elem_by_class($html_from_cache, "cache-element-4");

	//update slow elements with the cached ones
	$html_from_real_file = file_get_contents($_SERVER["SCRIPT_FILENAME"]);
	$html_from_real_file = update_elem_by_class_php($html_from_real_file, "cache-element-1", $element_1);
	$html_from_real_file = update_elem_by_class_php($html_from_real_file, "cache-element-2", $element_2);
	$html_from_real_file = update_elem_by_class_php($html_from_real_file, "cache-element-3", $element_3);
	$html_from_real_file = update_elem_by_class_php($html_from_real_file, "cache-element-4", $element_4);

	//return the patched html
	return $html_from_real_file;
}

function is_time_to_refresh_elements_cache_for_that_file($url)
{
	// Normalize the URL
	$url = normalize_url($url);
	$url = patch_url_allways_to_production($url);
	$file_for_time = generate_cache_elements_file($url);
	$current_time = time();

	// Read last refresh time
	if (file_exists($file_for_time)) {
		$last_refresh = (int) filemtime($file_for_time);
	} else {
		$last_refresh = 0;
	}

	$reset_file_path = $_SERVER['DOCUMENT_ROOT'] . '/urls-reset-ttfb.txt';
	$default_reset_time = 172800; // 48 hours in seconds (same as ttfb.php)

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

	// Check if reset time has passed since last refresh
	if (($current_time - $last_refresh) > $reset_time) {
		return true;
	}

	return false;
}

function get_url_without_cache_elements_params($original_cached_url)
{
	// Url without turn on and turn off
	$original_cached_url = str_replace("?turn-off-elements=1", "", $original_cached_url);
	$original_cached_url = str_replace("&turn-off-elements=1", "", $original_cached_url);
	$original_cached_url = str_replace("?turn-on-elements=1", "", $original_cached_url);
	$original_cached_url = str_replace("&turn-on-elements=1", "", $original_cached_url);
	$original_cached_url = str_replace("?turn-off-elements-all=1", "", $original_cached_url);
	$original_cached_url = str_replace("&turn-off-elements-all=1", "", $original_cached_url);
	$original_cached_url = str_replace("?turn-on-elements-all=1", "", $original_cached_url);
	$original_cached_url = str_replace("&turn-on-elements-all=1", "", $original_cached_url);
	return $original_cached_url;
}

// load functions
include_once __DIR__ . '/functions.php';
if (file_exists(__DIR__ . '/polyfills.php')) {
	include_once __DIR__ . '/polyfills.php';
}

// να μην κάνει τίποτα αμα ειναι μέσα στο admin
if (is_inside_any_admin() or is_iframe()) {
	return;
}

// Normalize the URL to exclude query parameters
$base_url = get_current_url_no_query();

// Normalize the URL to exclude query parameters
$original_cached_elements_url = get_current_url();

// Handle turn-off and turn-on parameters
$urls_cached_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-elements-cache.txt';
$cache_elements_all_state_file = $_SERVER['DOCUMENT_ROOT'] . '/urls-elements-cache-all.txt';

// Get the URL without cache control parameters
$urls_no_opts = get_url_without_cache_elements_params($original_cached_elements_url);
if (isset($_GET['turn-off-elements'])) {
	turn_off_cache_elements($base_url, $urls_cached_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-elements'])) {
	turn_on_cache_elements($base_url, $urls_cached_file, $urls_no_opts);
} elseif (isset($_GET['turn-off-elements-all'])) {
	turn_off_cache_elements_all($cache_elements_all_state_file, $urls_no_opts);
} elseif (isset($_GET['turn-on-elements-all'])) {
	turn_on_cache_elements_all($cache_elements_all_state_file, $urls_no_opts);
}

// Check for cached content
$cache_file = generate_cache_elements_file($base_url); //elem cache is working only with base url's

// Check if caching is turned off for the current URL
$caching_enabled_elements = cache_is_on_for_elements($base_url);
$cache_elements_all_enabled = cache_elements_all_is_on($cache_elements_all_state_file);
$caching_enabled_elements = ($caching_enabled_elements && $cache_elements_all_enabled);

// Check if is time to refresh
$refresh_elements_cache = is_time_to_refresh_elements_cache_for_that_file($base_url);

// Here we read from cache
if ($caching_enabled_elements == true && file_exists($cache_file) && $refresh_elements_cache == false) {

	//put the links
	ob_start('setup_cache_elements_links');

	//patch elements from cache
	$html_from_cache = file_get_contents($cache_file);
	$html_from_real_file = file_get_contents($_SERVER["SCRIPT_FILENAME"]);

	//patch cached elements
	$html_from_real_file = patch_cached_elements($html_from_cache, $html_from_real_file);

	//execute the new thing
	exit(eval ('?>' . $html_from_real_file)); // we are using exit here method
	//fixed: space on closing tag removed
}

// Here we write the cache
elseif ($caching_enabled_elements == true) {

	//put the links
	ob_start('setup_cache_elements_links');

	//start writing the cache
	ob_start('setup_cache_elements_output');
	return;
}

// Here we do nothing
else {

	//put the links
	ob_start('setup_cache_elements_links');
	return;
}
