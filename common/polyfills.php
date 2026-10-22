<?php
///////////////////////////////////////////////////////////////////////////
///PLATFORM FALLBACKS
///////////////////////////////////////////////////////////////////////////
if (!function_exists('str_contains')) {

	/**
	 * The function checks if a string contains a specific substring and returns a boolean value.
	 *
	 * @param string haystack The haystack parameter is a string that represents the main string in which
	 * we want to search for a substring.
	 * @param string The needle parameter is the substring that we are searching for within the haystack
	 * string.
	 *
	 * @return bool a boolean value. It will return true if the needle is found in the haystack string,
	 * and false otherwise.
	 */
	function str_contains($haystack, $needle)
	{
		return '' === $needle || false !== strpos($haystack, $needle);
	}
}

if (!function_exists('str_starts_with')) {

	/**
	 * The function checks if a string starts with a specific substring.
	 *
	 * @param string The string in which we want to check if it starts with the needle.
	 * @param string The string that we want to check if it is at the beginning of the haystack string.
	 *
	 * @return bool a boolean value. It returns true if the given haystack string starts with the given needle
	 * string, and false otherwise.
	 */
	function str_starts_with($haystack, $needle)
	{
		$length = strlen($needle);
		return substr($haystack, 0, $length) === $needle;
	}
}

if (!function_exists('str_ends_with')) {

	/**
	 * The function checks if a given string ends with a specific substring.
	 *
	 * @param string haystack The string in which we want to check if it ends with the needle.
	 * @param string needle The needle parameter is a string that we want to check if it is at the end of
	 * the haystack string.
	 *
	 * @return bool a boolean value. It returns true if the given string  ends with the string ,
	 * and false otherwise.
	 */
	function str_ends_with($haystack, $needle)
	{
		$length = strlen($needle);
		if (!$length) {
			return true;
		}
		return substr($haystack, -$length) === $needle;
	}
}

if (!function_exists('mb_strtolower')) {

	function mb_strtolower($str, $encoding = null)
	{
		return strtolower($str);
	}
}

if (!function_exists('mb_strtoupper')) {

	function mb_strtoupper($str, $encoding = null) //must build-in
	{

		return strtoupper($str);
	}
}

if (!defined('MB_CASE_UPPER')) {
	define('MB_CASE_UPPER', 0);
}
if (!defined('MB_CASE_LOWER')) {
	define('MB_CASE_LOWER', 1);
}
if (!defined('MB_CASE_TITLE')) {
	define('MB_CASE_TITLE', 2);
}
if (!function_exists('mb_convert_case')) {

	//να μην φύγει το error από εδώ
	function mb_convert_case($str, $mode = MB_CASE_UPPER, $encoding = null) //must build-in
	{
		if ($mode === MB_CASE_LOWER) {
			return strtolower($str);
		}

		if ($mode === MB_CASE_TITLE) {
			return ucwords(strtolower($str));
		}

		return strtoupper($str);
	}
}

if (!function_exists('mb_substr')) {
	function mb_substr($str, $start, $length = null, $encoding = null) //must build-in
	{
		if ($length === null) {
			return substr($str, $start);
		}

		return substr($str, $start, $length);
	}
}

if (!function_exists('mb_internal_encoding')) {
	function mb_internal_encoding($encoding = null)
	{
		static $internal = 'UTF-8';

		if ($encoding === null) {
			return $internal;
		}

		$internal = $encoding;
		return true;
	}
}

/**
 * This is a PHP 8 replacement function for the deprecated create_function function that allows developers to create anonymous functions at runtime using a string of PHP code as input.
 *
 * @param string The argument list for the anonymous function.
 * @param string The body of the anonymous function.
 * @return callable Returns a new anonymous function.
 */
if (!function_exists("create_function")) {
	function create_function($arg, $body)
	{
		static $cache = []; // A static array used to store previously created functions.
		static $max_cache_size = 64; // The maximum size of the cache.
		static $sorter; // A callback function used to sort the cache by hit count.

		if ($sorter === null) {

			//define the sorter callback function.
			$sorter = function ($a, $b) {
				if ($a->hits == $b->hits) {
					return 0;
				}
				return $a->hits < $b->hits ? 1 : -1;
			};
		}

		//generate a unique key for the current function.
		$crc = crc32($arg . "\\x00" . $body);
		if (isset($cache[$crc])) {

			//if the function has already been created and cached, increment the hit count and return the cached function.
			++$cache[$crc][1];
			return $cache[$crc][0];
		}

		if (sizeof($cache) >= $max_cache_size) {

			//if the cache size limit is reached, sort the cache by hit count and remove the least-used function.
			uasort($cache, $sorter);
			array_pop($cache);
		}

		//create a new anonymous function using `eval` and store it in the cache along with a hit count of 0.
		$cache[$crc] = [
			($cb = eval ("return function(" . $arg . "){" . $body . "};")),
			0,
		];
		return $cb;
	}
}

if (!function_exists('exif_imagetype')) {
	function exif_imagetype($filename)
	{
		if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
			return $type;
		}
		return false;
	}
}