<?php

// Μην φορτωθεί δεύτερη φορά
if (defined('AUTO_VERSION_BUFFER_LOADED')) {
	return;
}

define('AUTO_VERSION_BUFFER_LOADED', 1);

/*
 * Έλεγχος αν το URL είναι relative ή ακριβώς στο ίδιο domain.
 * External domains και subdomains δεν πειράζονται.
 */
function auto_version_is_same_domain($url)
{
	if (!preg_match('~^(https?:)?//~i', $url)) {
		return true;
	}

	if (substr($url, 0, 2) == '//') {
		$url = 'http:' . $url;
	}

	$parts = parse_url($url);

	if (!is_array($parts) || !isset($parts['host'])) {
		return false;
	}

	$current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

	// localhost:8080 -> localhost
	$current_host = preg_replace('/:\d+$/', '', $current_host);

	return strtolower($parts['host']) == strtolower($current_host);
}


/*
 * Βγάζει παλιό:
 *
 * ?v=123
 * ?version=123
 *
 * Κρατάει όλα τα άλλα query parameters.
 */
function auto_version_remove_old_version($query)
{
	if ($query == '') {
		return '';
	}

	$use_html_amp = stripos($query, '&amp;') !== false;

	$query = str_ireplace('&amp;', '&', $query);

	$parts = explode('&', $query);
	$clean = array();

	foreach ($parts as $part) {

		if ($part == '') {
			continue;
		}

		$pair = explode('=', $part, 2);
		$name = strtolower(urldecode($pair[0]));

		if ($name == 'v' || $name == 'version') {
			continue;
		}

		$clean[] = $part;
	}

	return implode($use_html_amp ? '&amp;' : '&', $clean);
}


/*
 * Βρίσκει το πραγματικό CSS/JS αρχείο στον server.
 */
function auto_version_find_file($path, $absolute_url)
{
	if (!isset($_SERVER['DOCUMENT_ROOT'])) {
		return false;
	}

	$root = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
	$path = rawurldecode($path);

	$candidates = array();


	// /web/style.css
	// https://same-domain.com/web/style.css
	if ($absolute_url || substr($path, 0, 1) == '/') {

		$candidates[] = $root . '/' . ltrim($path, '/\\');

	} else {

		/*
		 * Relative path σύμφωνα με το URL:
		 *
		 * css/style.css
		 * ../css/style.css
		 * ./style.css
		 */

		if (isset($_SERVER['REQUEST_URI'])) {

			$request = parse_url($_SERVER['REQUEST_URI']);

			$request_path = isset($request['path'])
				? $request['path']
				: '/';

			$request_dir = dirname($request_path);

			if ($request_dir == '.' || $request_dir == '\\') {
				$request_dir = '/';
			}

			$candidates[] = $root . '/' . trim($request_dir, '/\\') . '/' . $path;
		}


		// Fallback μέσω του πραγματικού PHP script
		if (isset($_SERVER['SCRIPT_FILENAME'])) {
			$candidates[] = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $path;
		}


		// Extra fallback
		if (isset($_SERVER['SCRIPT_NAME'])) {

			$script_dir = dirname($_SERVER['SCRIPT_NAME']);

			$candidates[] = $root . '/' . trim($script_dir, '/\\') . '/' . $path;
		}
	}


	foreach ($candidates as $candidate) {

		$real_file = realpath($candidate);

		if ($real_file !== false && is_file($real_file)) {
			return $real_file;
		}
	}

	return false;
}


/*
 * Φτιάχνει το τελικό URL με ?v=filemtime
 */
function auto_version_make_url($url)
{
	$original_url = $url;


	// External / subdomain
	if (!auto_version_is_same_domain($url)) {
		return $original_url;
	}


	$absolute_url = preg_match('~^(https?:)?//~i', $url) ? true : false;

	$path = '';
	$query = '';
	$fragment = '';
	$base_url = '';


	/*
	 * Absolute URL ίδιου domain
	 */
	if ($absolute_url) {

		$parse_url = $url;

		if (substr($parse_url, 0, 2) == '//') {
			$parse_url = 'http:' . $parse_url;
		}

		$parts = parse_url($parse_url);

		if (!is_array($parts) || !isset($parts['path'])) {
			return $original_url;
		}

		$path = $parts['path'];

		$query = isset($parts['query'])
			? $parts['query']
			: '';

		$fragment = isset($parts['fragment'])
			? '#' . $parts['fragment']
			: '';

		$base_url = preg_replace('~[?#].*$~', '', $original_url);

	}

	/*
	 * Relative / root-relative
	 */ else {

		$hash_pos = strpos($url, '#');

		if ($hash_pos !== false) {

			$fragment = substr($url, $hash_pos);

			$url = substr($url, 0, $hash_pos);
		}


		$question_pos = strpos($url, '?');

		if ($question_pos !== false) {

			$path = substr($url, 0, $question_pos);

			$query = substr($url, $question_pos + 1);

		} else {

			$path = $url;
		}

		$base_url = $path;
	}


	/*
	 * Μόνο CSS / JS
	 */
	$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

	if ($extension != 'css' && $extension != 'js') {
		return $original_url;
	}


	/*
	 * Βρες αρχείο
	 */
	$real_file = auto_version_find_file($path, $absolute_url);

	if ($real_file === false) {
		return $original_url;
	}


	/*
	 * Πάρε πάντα φρέσκο modification time
	 */
	clearstatcache();

	$version = filemtime($real_file);

	if ($version === false) {
		return $original_url;
	}


	/*
	 * Βγάλε προηγούμενο v/version
	 */
	$use_html_amp = stripos($query, '&amp;') !== false;

	$query = auto_version_remove_old_version($query);

	$separator = $use_html_amp ? '&amp;' : '&';


	/*
	 * Βάλε νέο version
	 */
	if ($query != '') {
		$query .= $separator;
	}

	$query .= 'v=' . $version;


	return $base_url . '?' . $query . $fragment;
}


/*
 * Callback για src/href
 */
function auto_version_attribute_callback($match)
{
	return $match[1] . $match[2] . auto_version_make_url($match[3]) . $match[2];
}


/*
 * Callback για ολόκληρο <script> ή <link>
 */
function auto_version_tag_callback($match)
{
	$tag = $match[0];


	// SCRIPT -> src
	if (preg_match('~^<\s*script\b~i', $tag)) {

		return preg_replace_callback(
			'~(\bsrc\s*=\s*)([\'"])(.*?)\2~is',
			'auto_version_attribute_callback',
			$tag,
			1
		);
	}


	// LINK -> href
	return preg_replace_callback(
		'~(\bhref\s*=\s*)([\'"])(.*?)\2~is',
		'auto_version_attribute_callback',
		$tag,
		1
	);
}


/*
 * Κεντρικό buffer
 */
function setup_auto_version_buffer($html)
{
	return preg_replace_callback(
		'~<\s*(?:script|link)\b(?:[^>"\']+|"[^"]*"|\'[^\']*\')*>~is',
		'auto_version_tag_callback',
		$html
	);
}


// Δικό του ανεξάρτητο buffer
ob_start('setup_auto_version_buffer');

?>