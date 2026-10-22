<?php
////////////////////// STRING FUNCTIONS //////////////////////

if (!function_exists('trim_last_character')) {
	function trim_last_character($str, $character)
	{
		return rtrim($str, $character);
	}
}

// Function to check if a URL is a static file type
if (!function_exists('is_static_type')) {
	function is_static_type($url)
	{
		// Extract the path from the URL
		$path = parse_url($url, PHP_URL_PATH);

		// List of static file extensions to skip
		$static_extensions = array(
			'.js',
			'.css',
			'.json',
			'.xml',
			'.txt',
			'.jpg',
			'.jpeg',
			'.png',
			'.gif',
			'.webp',
			'.svg',
			'.ico',
			'.pdf',
			'.zip',
			'.rar',
			'.7z',
			'.tar',
			'.gz',
			'.mp3',
			'.mp4',
			'.webm',
			'.mov',
			'.avi',
			'.flv',
			'.woff',
			'.woff2',
			'.ttf',
			'.eot',
			'.otf',
			'.map',
			'.min.js',
			'.min.css'
		);

		// Convert URL to lowercase for comparison
		$path_lower = strtolower($path);

		// Remove trailing slash for consistent checking
		$path_to_check = rtrim($path_lower, '/');

		// Check if the path ends with any static extension (with or without trailing slash)
		foreach ($static_extensions as $ext) {
			if (substr($path_to_check, -strlen($ext)) === $ext) {
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('days_in_month')) {
	function days_in_month($month, $year)
	{
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}
}

if (!function_exists('is_secure')) {
	function is_secure()
	{
		if (isset($_SERVER['HTTPS'])) {
			if ('on' == strtolower($_SERVER['HTTPS'])) {
				return true;
			} elseif ('1' == $_SERVER['HTTPS']) {
				return true;
			}
		}

		if (
			isset($_SERVER['SERVER_PORT']) &&
			('443' == $_SERVER['SERVER_PORT'])
		) {
			return true;
		}

		return false;
	}
}

if (!function_exists('remove_html_entities')) {
	function remove_html_entities($string)
	{
		$entities_to_remove = ['&#x27;', '&#x22;', '&#x5C;', '&#x2F;', '&#x3C;', '&#x3E;', '&#x60;', '&#x7B;', '&#x7D;', '&#x7C;', '&#x5E;', '&#x26;', '&#x24;', '&#x25;', '&#x40;', '&#x2A;', '&#x28;', '&#x29;', '&#x3B;', '&#x3A;', '&#x3F;', '&#x5B;', '&#x5D;', '&#x3D;', '&#x2B;', '&#x2D;', '&#x5F;', '&#x21;', '&#x23;', '&#x2C;', '&#x2E;'];
		foreach ($entities_to_remove as $entity) {
			$string = str_replace($entity, '', $string);
		}
		return $string;
	}
}

if (!function_exists('toAscii')) {
	function toAscii($str)
	{
		// patch - remove html entities first
		$str = remove_html_entities($str);

		// old code
		$str = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $str);
		$str = strtolower(trim($str, '_'));
		$str = preg_replace('/[\/_|+ -]+/', '_', $str);
		return $str;
	}
}

if (!function_exists('str_ends_count')) {
	function str_ends_count($string)
	{
		$c[0] = substr_count($string, "\n");
		$c[1] = substr_count($string, "\r\n");
		$c[2] = substr_count($string, "\r");
		return $c[0] + $c[1] + $c[2];
	}
}

if (!function_exists('slugify')) {
	function slugify($url, $sca = false)
	{
		// ελληνικά φωνήεντα
		$search = array('ου', 'ού', 'Ου', 'Ού', 'μπ', 'Μπ', 'έυ', 'εύ', 'ευ', 'Έυ', 'Εύ', 'Ευ', 'άυ', 'αύ', 'Αυ', 'Άυ', 'Αύ', 'Αυ');
		$replace = array('ou', 'ou', 'Ou', 'Ou', 'mp', 'B', 'ev', 'ev', 'ev', 'Ev', 'Ev', 'Ev', 'av', 'av', 'av', 'Av', 'Av', 'Av');
		$url = str_replace($search, $replace, $url);

		// κύριος μέρος με ελληνικά γράμματα
		$search = array('α', 'ά', 'Ά', 'Α', 'β', 'Β', 'γ', 'Γ', 'δ', 'Δ', 'ε', 'έ', 'Ε', 'Έ', 'ζ', 'Ζ', 'η', 'ή', 'Η', 'Ή', 'θ', 'Θ', 'ι', 'ί', 'ϊ', 'ΐ', 'Ι', 'Ί', 'κ', 'Κ', 'λ', 'Λ', 'μ', 'Μ', 'ν', 'Ν', 'ξ', 'Ξ', 'ο', 'ό', 'Ο', 'Ό', 'π', 'Π', 'ρ', 'Ρ', 'σ', 'ς', 'Σ', 'τ', 'Τ', 'υ', 'ύ', 'Υ', 'Ύ', 'φ', 'Φ', 'χ', 'Χ', 'ψ', 'Ψ', 'ω', 'ώ', 'Ω', 'Ώ', ' ', "'", "'", ',');
		$replace = array('a', 'a', 'A', 'A', 'v', 'V', 'g', 'G', 'd', 'D', 'e', 'e', 'E', 'E', 'z', 'Z', 'i', 'i', 'I', 'I', 'th', 'Th', 'i', 'i', 'i', 'i', 'I', 'I', 'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'ks', 'KS', 'o', 'o', 'O', 'O', 'p', 'P', 'r', 'R', 's', 's', 'S', 't', 'T', 'i', 'i', 'I', 'I', 'f', 'F', 'x', 'X', 'ps', 'Ps', 'o', 'o', 'O', 'O', ' ', "'", ',', null);
		$url = str_replace($search, $replace, $url);

		if ($sca == true) {
			// pack for scandinavian language
			$url = iconv('UTF-8', 'ASCII//TRANSLIT', $url);
			$url = preg_replace('~[^\pL0-9_]+~u', '', $url);  // use nothing instead of '-' for scandinavian
			$url = strtolower($url);
			return $url;
		}

		// Prep string with some basic normalization
		$url = strtolower($url);
		$url = strip_tags($url);
		$url = stripslashes($url);
		$url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');

		// Remove quotes (can't, etc.)
		$url = str_replace("'", '', $url);

		// Replace non-alpha numeric with hyphens
		$match = '/[^a-z0-9]+/';
		$replace = '-';
		$url = preg_replace($match, $replace, $url);
		$url = trim($url, '-');
		return $url;
	}
}

if (!function_exists('slugify_2')) {
	function slugify_2($text, $divider = '-')
	{
		// replace non letter or digits by divider
		$text = preg_replace('~[^\pL\d]+~u', $divider, $text);

		// transliterate
		$text = @iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, $divider);

		// remove duplicate divider
		$text = preg_replace('~-+~', $divider, $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}
}

if (!function_exists('greeklish')) {
	function greeklish($string)
	{
		$g2l['ου'] = 'ou';
		$g2l['Ου'] = 'Ou';
		$g2l['ού'] = 'ou';
		$g2l['Ού'] = 'Ou';
		$g2l['α'] = 'a';
		$g2l['ά'] = 'a';
		$g2l['Α'] = 'A';
		$g2l['Ά'] = 'A';
		$g2l['β'] = 'v';
		$g2l['Β'] = 'V';
		$g2l['γ'] = 'g';
		$g2l['Γ'] = 'G';
		$g2l['δ'] = 'd';
		$g2l['Δ'] = 'D';
		$g2l['ε'] = 'e';
		$g2l['έ'] = 'e';
		$g2l['Ε'] = 'E';
		$g2l['Έ'] = 'E';
		$g2l['ζ'] = 'z';
		$g2l['Ζ'] = 'Z';
		$g2l['η'] = 'i';
		$g2l['ή'] = 'i';
		$g2l['Η'] = 'I';
		$g2l['Ή'] = 'I';
		$g2l['θ'] = 'th';
		$g2l['Θ'] = 'Th';
		$g2l['ι'] = 'i';
		$g2l['ί'] = 'i';
		$g2l['ϊ'] = 'i';
		$g2l['ΐ'] = 'i';
		$g2l['Ι'] = 'I';
		$g2l['Ί'] = 'I';
		$g2l['Ϊ'] = 'I';
		$g2l['κ'] = 'k';
		$g2l['Κ'] = 'K';
		$g2l['λ'] = 'l';
		$g2l['Λ'] = 'L';
		$g2l['μ'] = 'm';
		$g2l['Μ'] = 'M';
		$g2l['ν'] = 'n';
		$g2l['Ν'] = 'N';
		$g2l['ξ'] = 'ks';
		$g2l['Ξ'] = 'Ks';
		$g2l['ο'] = 'o';
		$g2l['ό'] = 'o';
		$g2l['Ο'] = 'O';
		$g2l['Ό'] = 'O';
		$g2l['π'] = 'p';
		$g2l['Π'] = 'P';
		$g2l['ρ'] = 'r';
		$g2l['Ρ'] = 'R';
		$g2l['σ'] = 's';
		$g2l['ς'] = 's';
		$g2l['Σ'] = 'S';
		$g2l['τ'] = 't';
		$g2l['Τ'] = 'T';
		$g2l['υ'] = 'u';
		$g2l['ύ'] = 'u';
		$g2l['ϋ'] = 'u';
		$g2l['ΰ'] = 'u';
		$g2l['Υ'] = 'U';
		$g2l['Ύ'] = 'U';
		$g2l['Ϋ'] = 'I';
		$g2l['φ'] = 'f';
		$g2l['Φ'] = 'F';
		$g2l['χ'] = 'h';
		$g2l['Χ'] = 'H';
		$g2l['ψ'] = 'ps';
		$g2l['Ψ'] = 'Ps';
		$g2l['ω'] = 'o';
		$g2l['ώ'] = 'o';
		$g2l['Ω'] = 'O';
		$g2l['Ώ'] = 'O';
		$g2l[' '] = ' ';
		$g2l['-'] = '-';
		$g2l['.'] = '.';
		$g2l['/'] = '/';
		$output = strtr($string, $g2l);
		return $output;
	}
}

if (!function_exists('str_match_words_that_starts_with')) {
	function str_match_words_that_starts_with($html, $starts_with)
	{
		$array = array();
		$regex = '~(' . preg_quote($starts_with) . '\w+)~';
		if (preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER)) {
			foreach ($matches[1] as $word) {
				$array[] = $word;
			}
		}

		return $array;
	}
}

if (!function_exists('str_read_until')) {
	function str_read_until($string, $character)
	{
		$string = explode($character, $string);
		$string = $string[0];
		return $string;
	}
}

if (!function_exists('str_read_after')) {
	function str_read_after($string, $character)
	{
		if (($pos = strpos($string, $character)) !== false) {
			$whatIWant = substr($string, $pos);
			return $whatIWant;
		} else {
			return false;
		}
	}
}

if (!function_exists('str_strip_numbers')) {
	function str_strip_numbers($str)
	{
		$str = preg_replace('/[0-9]+/', '', $str);
		return $str;
	}
}

if (!function_exists('str_keep_numbers')) {
	function str_keep_numbers($value)
	{
		$value = preg_replace('/\D/', '', $value);
		return $value;
	}
}

if (!function_exists('str_keep_numbers_special')) {
	function str_keep_numbers_special($data)
	{
		// keep numbers with commas, κρατάει το αμερικάνικο κόμμα που είναι τελεία
		$data = preg_replace('/[^0-9.-]/', '', $data);
		return $data;
	}
}

if (!function_exists('str_between')) {
	function str_between($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) {
			return '';
		}

		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
}

if (!function_exists('str_between_all')) {
	function str_between_all($content, $start, $end)
	{
		$n = explode($start, $content);
		$result = array();

		$i = 0;
		foreach ($n as $val) {
			$pos = strpos($val, $end);
			if ($pos !== false) {
				if ($i != 0) {
					$result[] = substr($val, 0, $pos);
				}
			}
			$i++;
		}
		return $result;
	}
}

if (!function_exists('str_between_reverse')) {
	function str_between_reverse($string, $start, $end)
	{
		$string = str_between(strrev($string), strrev($end), strrev($start));
		return strrev($string);
	}
}

if (!function_exists('str_between_reverse_all')) {
	function str_between_reverse_all($string, $start, $end)
	{
		$array = str_between_all(strrev($string), strrev($end), strrev($start));
		foreach ($array as $item) {
			$array_new[] = strrev($item);
		}
		rsort($array_new);
		return $array_new;
	}
}

if (!function_exists('str_replace_between')) {
	function str_replace_between($search_start, $search_end, $replace, $content)
	{
		$search_results = str_between_all($content, $search_start, $search_end);
		foreach ($search_results as $search_item) {
			$search = $search_start . $search_item . $search_end;
			if ($replace != null) {
				$content = str_replace($search, $replace, $content);
			}
		}
		return $content;
	}
}

if (!function_exists('str_replace_between_reverse')) {
	function str_replace_between_reverse($search_start, $search_end, $replace, $content)
	{
		$search_start_reverse = strrev($search_start);
		$search_end_reverse = strrev($search_end);
		$content_reverse = strrev($content);
		$search_results = str_between_all($content_reverse, $search_start_reverse, $search_end_reverse);
		foreach ($search_results as $search_item) {
			$search = strrev($search_start_reverse . $search_item . $search_end_reverse);
			$content = str_replace($search, $replace, $content);
		}
		return $content;
	}
}

if (!function_exists('str_replace_first')) {
	function str_replace_first($search, $replace, $subject)
	{
		// old method for php7
		// $search = '/' . preg_quote($search, '/') . '/';
		// return preg_replace($search, $replace, $subject, 1);

		// new method for php8
		$pos = strpos($subject, $search);
		if ($pos !== false) {
			$new_string = substr_replace($subject, $replace, $pos, strlen($search));
			return $new_string;
		} else {
			return $subject;
		}
	}
}

if (!function_exists('str_contains_in_array')) {
	function str_contains_in_array($haystack_array, $needle_elem)
	{
		return '' === $needle_elem || false !== strpos(implode(' ,', $haystack_array), $needle_elem);
	}
}

if (!function_exists('str_starts_with_number')) {
	function str_starts_with_number($str)
	{
		return preg_match('/^\d/', $str) === 1;
	}
}

if (!function_exists('str_ends_with_newline')) {
	function str_ends_with_newline($str)
	{
		$found = false;
		foreach (array("\r", "\n", "\r\n", "\n\r") as $token) {
			$desiredPos = strlen($str) - strlen($token);
			if (strpos($str, $token) === $desiredPos) {
				$found = true;
				break;
			}
		}
		return $found;
	}
}

if (!function_exists('strip_accents')) {
	function strip_accents($str)
	{
		$a = array('ά', 'έ', 'ή', 'ί', 'ύ', 'ό', 'ώ', 'ς', 'Ά', 'Έ', 'Ή', 'Ί', 'Ύ', 'Ό', 'Ώ');
		$b = array('α', 'ε', 'η', 'ι', 'υ', 'ο', 'ω', 'Σ', 'Α', 'Ε', 'Η', 'Ι', 'Υ', 'Ο', 'Ω');
		return str_replace($a, $b, $str);
	}
}

if (!function_exists('strip_tags_with_whitespace')) {
	function strip_tags_with_whitespace($string, $allowable_tags = null)
	{
		$string = str_replace('<', ' <', $string);
		$string = strip_tags($string, $allowable_tags);
		$string = str_replace('  ', ' ', $string);
		$string = trim($string);
		return $string;
	}
}

if (!function_exists('is_svg')) {
	function is_svg($path)
	{
		if (function_exists('mime_content_type')) {
			return 'image/svg+xml' === mime_content_type($path);
		} else {
			return false;
		}
	}
}

if (!function_exists('is_image')) {
	function is_image($path)
	{
		// Define an array of valid pure image extensions
		$valid_extensions = [
			'jpg',
			'jpeg',
			'png',
			'gif',
			'bmp',
			'webp',
			'svg',
			'tiff',
			'tif',
			'ico',
			'heic',
			'heif',
			'avif',
			'jfif',
			'pjpeg',
			'pjp',
			'svgz'
		];

		// Get the file extension of the path
		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

		// Check if the extension is in the array of valid extensions
		if (in_array($extension, $valid_extensions)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('is_video')) {
	function is_video($path)
	{
		// List of common video file extensions
		$video_extensions = [
			'mp4',
			'avi',
			'mov',
			'wmv',
			'mkv',
			'flv',
			'webm',
			'm4v',
			'3gp',
			'3g2',
			'mpeg',
			'mpg',
			'mpe',
			'mpv',
			'ogg',
			'ogv'
		];

		// Get the file extension
		$file_extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

		// Check if the file extension is in the list of video extensions
		return in_array($file_extension, $video_extensions);
	}
}

if (!function_exists('is_audio')) {
	function is_audio($path)
	{
		$audio_extensions = [
			'mp3',
			'wav',
			'flac',
			'aac',
			'ogg',
			'wma',
			'm4a',
			'aiff',
			'alac',
			'opus',
			'amr',
			'mid',
			'midi',
			'ra',
			'rm',
			'aif',
			'aifc',
			'caf',
			'snd',
			'au',
			'mpc',
			'wv',
			'ape',
			'dsf',
			'dff',
			'oga',
			'mogg'
		];

		$file_extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

		return in_array($file_extension, $audio_extensions);
	}
}

if (!function_exists('mb_ucfirst')) {

	//this is not a fallback
	if (!function_exists('mb_ucfirst')) {
		function mb_ucfirst(string $str)
		{

			$fc = mb_strtoupper(mb_substr($str, 0, 1));
			return $fc . mb_substr($str, 1);
		}
	}
}

if (!function_exists('mb_ucwords')) {

	function mb_ucwords(string $s)
	{

		$arr = preg_split("//u", $s, -1, PREG_SPLIT_NO_EMPTY);
		$result = "";
		$mode = false;
		foreach ($arr as $char) {
			$res = preg_match(
				'/\\p{Mn}|\\p{Me}|\\p{Cf}|\\p{Lm}|\\p{Sk}|\\p{Lu}|\\p{Ll}|' .
				'\\p{Lt}|\\p{Sk}|\\p{Cs}/u',
				$char
			) == 1;
			if ($mode) {
				if (!$res) {
					$mode = false;
				}
			} elseif ($res) {
				$mode = true;
				$char = mb_convert_case($char, MB_CASE_TITLE, "UTF-8");
			}
			$result .= $char;
		}

		return $result;
	}
}

if (!function_exists('is_image_by_extension')) {
	function is_image_by_extension($path)
	{

		if ($path == null) {

			return false;
		}

		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'avif', 'tiff', 'svg', 'ico'])) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('cleanString')) {
	function cleanString($text)
	{
		$utf8 = array(
			'/[áàâãªä]/u' => 'a',
			'/[ÁÀÂÃÄ]/u' => 'A',
			'/[ÍÌÎÏ]/u' => 'I',
			'/[íìîï]/u' => 'i',
			'/[éèêë]/u' => 'e',
			'/[ÉÈÊË]/u' => 'E',
			'/[óòôõºö]/u' => 'o',
			'/[ÓÒÔÕÖ]/u' => 'O',
			'/[úùûü]/u' => 'u',
			'/[ÚÙÛÜ]/u' => 'U',
			'/ç/' => 'c',
			'/Ç/' => 'C',
			'/ñ/' => 'n',
			'/Ñ/' => 'N',
			'/–/' => '-',  // UTF-8 hyphen to "normal" hyphen
			'/[’‘‹›‚]/u' => ' ',  // Literally a single quote
			'/[“”«»„]/u' => ' ',  // Double quote
			'/ /' => ' ',  // nonbreaking space (equiv. to 0x160)
		);
		return preg_replace(array_keys($utf8), array_values($utf8), $text);
	}
}

if (!function_exists('minify')) {
	function minify($data)
	{
		$data = str_replace('  ', ' ', $data);
		$data = str_replace("\r\n", ' ', $data);
		$data = str_replace("\n", ' ', $data);
		$data = str_replace("\r", ' ', $data);
		$data = str_replace("\t", ' ', $data);
		return $data;
	}
}

if (!function_exists('sanitize_output')) {
	function sanitize_output($html)
	{
		$search = array(
			'/\>[^\S ]+/s',
			'/[^\S ]+\</s',
			'/(\s)+/s',
			'/<!--(.|\s)*?-->/',  // Remove HTML comments
		);
		$replace = array(
			'>',
			'<',
			'\1',
			'',
		);
		$html = preg_replace($search, $replace, $html);
		return $html;
	}
}

if (!function_exists('convert2flat')) {
	function convert2flat($html)
	{
		// toAscii
		$html = toAscii($html);

		// to lower
		$html = mb_strtolower($html);
		$html = strtolower($html);

		// keep only numbers and letters
		$html = preg_replace('/[^a-zA-Z0-9]+/', '', $html);
		return $html;
	}
}

if (!function_exists('greek2englishdate')) {
	function greek2englishdate($date)
	{
		$date = str_replace('Ιανουάριος', 'January', $date);
		$date = str_replace('Φεβρουάριος', 'February', $date);
		$date = str_replace('Μάρτιος', 'March', $date);
		$date = str_replace('Απρίλιος', 'April', $date);
		$date = str_replace('Μάιος', 'May', $date);
		$date = str_replace('Ιούνιος', 'June', $date);
		$date = str_replace('Ιούλιος', 'July', $date);
		$date = str_replace('Αύγουστος', 'August', $date);
		$date = str_replace('Σεπτέμβριος', 'September', $date);
		$date = str_replace('Οκτώβριος', 'October', $date);
		$date = str_replace('Νοεμβρίου', 'November', $date);
		$date = str_replace('Δεκέμβριος', 'December', $date);
		return $date;
	}
}

if (!function_exists('english2greekdate')) {
	function english2greekdate($date)
	{
		$date = str_replace('January', 'Ιανουάριος', $date);
		$date = str_replace('February', 'February', $date);
		$date = str_replace('March', 'Μάρτιος', $date);
		$date = str_replace('April', 'Απρίλιος', $date);
		$date = str_replace('May', 'Μάιος', $date);
		$date = str_replace('June', 'Ιούνιος', $date);
		$date = str_replace('July', 'Ιούλιος', $date);
		$date = str_replace('August', 'Αύγουστος', $date);
		$date = str_replace('September', 'Σεπτέμβριος', $date);
		$date = str_replace('October', 'Οκτώβριος', $date);
		$date = str_replace('November', 'Νοεμβρίου', $date);
		$date = str_replace('December', 'Δεκέμβριος', $date);
		return $date;
	}
}

if (!function_exists('translate_text_yandex')) {
	function translate_text_yandex($text, $output_lang_code = 'en')
	{
		load_enviroment_variables(dirname(__DIR__) . '/.env');
		$key = getenv('YANDEX_TRANSLATE_API_KEY');
		if ($key === false || trim($key) === '') {
			throw new RuntimeException('Missing YANDEX_TRANSLATE_API_KEY in .env.');
		}

		$text = rawurlencode($text);
		$url = "https://translate.yandex.net/api/v1.5/tr/translate?key=$key&text=$text&lang=$output_lang_code&format=plain";
		$xml = file_get_contents($url);

		// if file_get_contents work then
		if (strpos($xml, '<text>') !== false) {
			$output = $xml;
			$output = str_between($output, '<text>', '</text>');
			$output = strip_tags($output);
			return $output;
		} else {
			// else url CURL
			$xml_curl = file_get_contents_curl($url, $fields = array());

			$output = $xml_curl;
			$output = str_between($output, '<text>', '</text>');
			$output = strip_tags($output);
			return $output;
		}
	}
}

if (!function_exists('is_binary')) {
	function is_binary($content)
	{
		if (empty($content)) {
			return false;
		}

		// Define a threshold for the percentage of non-printable characters
		$threshold = 0.2; // 20% of the content

		$len = strlen($content);
		$non_printable_count = 0;

		for ($i = 0; $i < $len; $i++) {
			// Check if the character is non-printable (ASCII < 32 or > 126)
			$char = $content[$i];
			if (ord($char) < 32 || ord($char) > 126) {
				$non_printable_count++;
			}

			// If the count exceeds the threshold, consider it binary
			if ($non_printable_count / $len > $threshold) {
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('is_text')) {
	function is_text(string $filename): bool
	{
		$textExtensions = [
			// Common text files
			'txt',
			'log',
			'csv',
			'md',
			'rtf',
			'diff',
			'patch',
			'text',
			'asc',
			'pot',
			'readme',
			'changes',
			'changelog',
			'news',
			'install',
			'todo',
			'license',
			'authors',
			'contributors',

			// Web development
			'html',
			'htm',
			'xhtml',
			'css',
			'scss',
			'sass',
			'less',
			'js',
			'jsx',
			'ts',
			'tsx',
			'json',
			'xml',
			'yaml',
			'yml',
			'svg',
			'php',
			'phtml',
			'asp',
			'aspx',
			'jsp',
			'vue',
			'twig',
			'mustache',
			'handlebars',
			'hbs',
			'ejs',
			'haml',
			'jade',
			'pug',
			'liquid',
			'cshtml',
			'razor',
			'mjs',
			'cjs',
			'webmanifest',

			// Programming languages
			'c',
			'cpp',
			'h',
			'hpp',
			'cs',
			'java',
			'py',
			'rb',
			'pl',
			'sql',
			'sh',
			'bash',
			'r',
			'swift',
			'go',
			'rust',
			'kt',
			'scala',
			'lua',
			'perl',
			'vb',
			'ps1',
			'psm1',
			'f90',
			'f95',
			'f03',
			'nim',
			'zig',
			'tcl',
			'ml',
			'mli',
			'erl',
			'ex',
			'exs',
			'clj',
			'cls',
			'groovy',
			'gvy',
			'hs',
			'lhs',
			'pas',
			'p',
			'pp',
			'inc',
			'm',
			'mm',
			'rex',
			'rexx',
			'raku',
			'rakumod',
			'rakudoc',
			'dart',
			'v',
			'ada',
			'adb',
			'ads',
			'vhdl',
			'vhd',
			'fs',
			'fsx',
			'fsi',
			'fsscript',
			'gradle',
			'jl',
			'elm',
			'el',
			'lisp',
			'lsp',
			'scm',
			'ss',
			'rkt',
			'rktl',
			'rktd',
			'scrbl',
			'asm',
			'nasm',

			// Operating system scripts
			'bat',
			'cmd',
			'ps1',
			'vbs',
			'sh',
			'bash',
			'zsh',
			'fish',
			'csh',
			'tcsh',
			'ksh',
			'rc',
			'profile',
			'bashrc',
			'zshrc',
			'fishrc',
			'inputrc',
			'login',
			'logout',
			'cshrc',
			'zlogin',
			'zlogout',
			'zprofile',
			'dircolors',
			'minttyrc',
			'curlrc',
			'wgetrc',
			'npmrc',
			'yarnrc',
			'screenrc',
			'vimrc',
			'gvimrc',
			'bash_profile',
			'bash_logout',
			'bash_aliases',
			'powershell',

			// Configuration files
			'ini',
			'conf',
			'config',
			'cfg',
			'env',
			'properties',
			'htaccess',
			'htpasswd',
			'nginx',
			'dockerignore',
			'lock',
			'gitignore',
			'npmrc',
			'babelrc',
			'eslintrc',
			'passwd',
			'prettierrc',
			'editorconfig',
			'cnf',
			'service',
			'shadow',
			'desktop',
			'policy',
			'rules',
			'path',
			'exports',
			'sudoers',
			'hosts',
			'hostname',
			'fstab',
			'group',
			'crontab',
			'mime.types',
			'resolv.conf',
			'sshd_config',
			'ssh_config',
			'dhcpd.conf',
			'dnsmasq.conf',
			'my.cnf',
			'php.ini',
			'httpd.conf',
			'xorg.conf',

			// Build and deployment
			'cmake',
			'make',
			'mak',
			'mk',
			'rakefile',
			'dockerfile',
			'vagrantfile',
			'jenkinsfile',
			'gemfile',
			'modulefile',
			'procfile',
			'apspec',
			'bzl',
			'bazel',
			'workflow',
			'gitlab-ci',
			'travis',
			'appveyor',
			'azure-pipelines',
			'Makefile',
			'ninja',
			'pom',
			'build',
			'gradle',
			'ant',
			'ivy',
			'maven',
			'sbt',

			// Documentation
			'tex',
			'rst',
			'adoc',
			'textile',
			'wiki',
			'man',
			'markdown',
			'mkd',
			'mkdn',
			'mdwn',
			'mdown',
			'mdtxt',
			'mdtext',
			'pod',
			'dokuwiki',
			'mediawiki',
			'creole',
			'org',
			'rdoc',
			'roff',
			'asciidoc',
			'texi',
			'texinfo',

			// Data formats
			'csv',
			'tsv',
			'toml',
			'properties',
			'dist',
			'template',
			'tml',
			'raml',
			'wadl',
			'xaml',
			'xslt',
			'xsl',
			'xsd',
			'dtd',
			'rnc',
			'rng',
			'sch',
			'ldif',
			'graph',
			'dot',
			'gql',
			'graphql',
			'ndjson',
			'jsonl',
			'geojson',
			'regex',

			// System files
			'pid',
			'lock',
			'socket',
			'device',
			'mount',
			'timer',
			'target',
			'snapshot',
			'backup',
			'swp',
			'tmp',
			'temp',
			'state',
			'cache',
			'log',
			'run',
			'running',
			'status',
			'lease',
			'table',
			'dat',
			'local',
			'opts',
			'prefs'
		];

		$fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		return in_array($fileExtension, $textExtensions, true);
	}
}

///////////////////// ARRAY FUNCTIONS //////////////////////

if (!function_exists('oneStepSmaller')) {
	function oneStepSmaller($number, $candidates)
	{
		$last = null;
		rsort($candidates);
		foreach ($candidates as $cand) {
			if ($cand < $number) {
				$last = $cand;
				break;
			}
		}
		return $last;
	}
}

if (!function_exists('oneStepBigger')) {
	function oneStepBigger($number, $candidates)
	{
		$last = null;
		sort($candidates);
		foreach ($candidates as $cand) {
			if ($cand > $number) {
				$last = $cand;
				break;
			}
		}
		return $last;
	}
}

if (!function_exists('array_remove_empty_values')) {
	function array_remove_empty_values($array)
	{
		return array_filter($array, 'array_remove_empty_values_internal');
	}
}

if (!function_exists('array_remove_empty_values_internal')) {
	function array_remove_empty_values_internal($value)
	{
		return !empty($value) || $value === 0;
	}
}

if (!function_exists('array_remove_duplicate_values')) {
	function array_remove_duplicate_values($array)
	{
		return array_unique($array);
	}
}

if (!function_exists('array_push_top')) {
	function array_push_top($array, $value)
	{
		array_unshift($array, $value);
	}
}

if (!function_exists('sortByChangeTime')) {
	function sortByChangeTime($file1, $file2)
	{
		// compatable for php 5
		$time1 = filectime($file1);
		$time2 = filectime($file2);

		if ($time1 == $time2) {
			return 0;
		}
		return ($time1 < $time2) ? -1 : 1;

		// was php 7 and 8
		// return(filectime($file1) <=> filectime($file2)); //was <
	}
}

if (!function_exists('sortByStringLength')) {
	function sortByStringLength($a, $b)
	{
		return strlen($b) - strlen($a);
	}
}

if (!function_exists('str_replace_in_array')) {
	function str_replace_in_array($search, $replace, $array)
	{
		$new_array = array();
		foreach ($array as $k => $v) {
			// καμιά φορά έχει array μέσα στο array για αυτό έβαλα αυτό
			if (is_array($v)) {
				continue;
			}

			$k = str_replace($search, $replace, $k);
			$v = str_replace($search, $replace, $v);
			$new_array[$k] = $v;
		}
		return $new_array;
	}
}

if (!function_exists('array_merge_insensitive')) {
	function array_merge_insensitive($array1, $array2)
	{
		$merged = $array1;

		foreach ($array2 as $key => $value) {
			$lower_key = strtolower($key);
			$found = false;

			foreach ($merged as $merged_key => $merged_value) {
				if (strtolower($merged_key) == $lower_key) {
					$merged[$merged_key] = $value;
					$found = true;
					break;
				}
			}

			if (!$found) {
				$merged[$key] = $value;
			}
		}

		return $merged;
	}
}

if (!function_exists('array_merge_cross_extensions')) {
	function array_merge_cross_extensions(...$arrays)
	{

		// Combine all the arrays
		$all_files = [];
		foreach ($arrays as $array) {
			$all_files = array_merge($all_files, $array);
		}

		// Create an associative array to store unique files
		$unique_files = [];

		foreach ($all_files as $file) {
			$filename_without_extension = pathinfo($file, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($file, PATHINFO_FILENAME);
			$extension = pathinfo($file, PATHINFO_EXTENSION);

			// If the filename without extension is not already in the array, add it
			if (!isset($unique_files[$filename_without_extension])) {
				$unique_files[$filename_without_extension] = $file;
			}
		}

		// Get the final array of files
		return array_values($unique_files);
	}
}

if (!function_exists('array_unique_cross_greek_characters')) {
	function array_unique_cross_greek_characters($array)
	{
		$normalized = [];
		$result = [];

		foreach ($array as $key => $value) {
			$simple = str_replace(
				['ά', 'έ', 'ή', 'ί', 'ό', 'ύ', 'ώ', 'Ά', 'Έ', 'Ή', 'Ί', 'Ό', 'Ύ', 'Ώ'],
				['α', 'ε', 'η', 'ι', 'ο', 'υ', 'ω', 'Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω'],
				(string) $value
			);

			// Use the normalized value as a key to ensure uniqueness
			if (!isset($normalized[$simple])) {
				$normalized[$simple] = $key;
				$result[$key] = $value;
			}
		}

		return $result;
	}
}

//////////////////////////// URL FUNCTIONS ////////////////////////////

if (!function_exists('get_url_basename')) {
	function get_url_basename($url)
	{
		$url = str_read_after($url, '://');
		$url = explode('#', $url);
		$url = $url[0];
		$url = explode('?', $url);
		$url = $url[0];
		$url = basename($url);
		return $url;
	}
}

if (!function_exists('patch_url_with_trailing_slash')) {
	function patch_url_with_trailing_slash($url, $do_not_check_url = false)
	{
		// must accept only urls because we don't use path trailing slash on paths
		if (!is_full_url($url) && $do_not_check_url == false) {
			return $url;
		}

		// if is // only then add https
		if (str_starts_with($url, '//')) {
			if (isset($_SERVER['HTTPS']) or is_cli()) {
				$url = 'https:' . $url;
			} else {
				$url = 'http:' . $url;
			}
		}

		// if already have a trailing slash then return same
		if (str_ends_with($url, '/')) {
			return $url;
		} elseif (str_ends_with($url, '.')) {
			return $url;
		} else {
			// if file have extension then return same
			$basename = basename($url);
			if (array_key_exists('extension', pathinfo($basename))) {
				$ext = pathinfo($url);
				$ext = $ext['extension'];
			} else {
				$ext = null;
			}

			if ($ext != null) {
				return $url;
			} else {
				// if url file have no extension and no trailing slash then return it with a slash
				return $url . '/';
			}
		}
	}
}

if (!function_exists('patch_url_if_ending_with_questionmark')) {
	function patch_url_if_ending_with_questionmark($url)
	{
		// if url is ending in questionmark then remove it
		if (str_ends_with($url, '?')) {
			$url = substr($url, 0, -1);
		}

		return $url;
	}
}

if (!function_exists('patch_url_if_is_localhost_or_not')) {
	function patch_url_if_is_localhost_or_not($url)
	{

		// This function is old and not in use anymore, patch for this URL
		if (is_localhost()) {
			$domain = get_domain_from_url($url);
			$url = str_replace("/" . $domain, "/localhost:8000", $url);
			$url = str_replace("https://", "http://", $url); //no https on localhost
		} else {
			$url = str_replace("/localhost:8000", "/" . $_SERVER['HTTP_HOST'], $url);
		}

		return $url;
	}
}

if (!function_exists('patch_url_allways_to_production')) {
	function patch_url_allways_to_production($url)
	{

		// This function is old and not in use anymore, patch for this URL
		if (is_localhost()) {

			$domain = basename(dirname(dirname(__FILE__)));
			$url = str_replace("/localhost:8000", "/" . $domain, $url);
			$url = str_replace("http://", "https://", $url); //no https on localhostZ
		} else {
			$url = str_replace("/localhost:8000", "/" . $_SERVER['HTTP_HOST'], $url);
			$url = str_replace("http://", "https://", $url); //no https on localhost
		}

		return $url;
	}
}

if (!function_exists('patch_url_with_https')) {
	function patch_url_with_https($url)
	{
		$url = str_replace('https://www.', 'https://', $url);
		$url = str_replace('http://www.', 'https://', $url);
		$url = str_replace('http://', 'https://', $url);
		return $url;
	}
}

if (!function_exists('site_url_des')) {
	function site_url_des()
	{
		// Detect the protocol
		$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $port == 443) ? "https://" : "http://";

		// Construct the base URL
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : basename(dirname(__DIR__));
		$default_url = $protocol . $host;

		return $default_url;
	}
}

if (!function_exists('normalize_url')) {
	function normalize_url($url)
	{

		//patch
		if (!isset($_SERVER['HTTP_HOST'])) {

			$_SERVER['HTTP_HOST'] = null;
		}

		// Remove protocol
		$url = preg_replace('(^https?://(www\.)?)', '', $url, 1);

		// Replace localhost with current host
		$url = str_replace("localhost:8000/", $_SERVER['HTTP_HOST'] . "/", $url);

		// Remove index.html or index.php only if they are at the end or followed by a query string
		$url = preg_replace('/\/index\.(html|php)(?=\?|$)/', '/', $url);

		// Decode
		$url = urldecode($url);

		// Put back the running protocol
		$url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http') . "://" . $url;

		return $url;
	}
}

// Function to normalize the URL
if (!function_exists('normalize_url_2')) {
	function normalize_url_2($url)
	{
		// Parse the URL to get the components
		$parsed_url = parse_url($url);

		// If the URL is syntactically incorrect, return false to cancel caching
		if ($parsed_url === false) {
			return false;
		}

		// Remove the fragment (#hash) if it exists
		unset($parsed_url['fragment']);

		// Normalize the path by removing 'index.html', 'index.php', etc.
		$path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$path = preg_replace('/\/index\.(html|php)$/', '/', $path);

		// Decode URL-encoded characters
		$path = urldecode($path);

		// Lowercase the path
		$path = strtolower($path);

		// Normalize the query string and ensure keys are in alphabetical order
		$query = '';
		if (isset($parsed_url['query'])) {
			parse_str($parsed_url['query'], $query_params);
			// Remove unwanted parameters
			$query_params = array_filter(
				$query_params,
				function ($key) {
					return $key !== 'turn-off' && $key !== 'turn-on' && $key !== 'refresh-cache';
				},
				ARRAY_FILTER_USE_KEY
			);
			// Sort the query parameters by keys to ensure consistent order
			ksort($query_params);
			$query = !empty($query_params) ? '?' . http_build_query($query_params) : '';
		}

		// Normalize the host (handle both www and non-www versions)
		$host = isset($parsed_url['host']) ? strtolower($parsed_url['host']) : '';
		if (strpos($host, 'www.') === 0) {
			$host = substr($host, 4);
		}

		// Preserve the port if it exists
		$port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';

		// Rebuild the URL without the fragment, patch query if empty
		$normalized_url = 'https://' . $host . $port . $path . $query;

		// Remove index.html or index.php only if they are at the end or followed by a query string
		$normalized_url = preg_replace('/\/index\.(html|php)(?=\?|$)/', '/', $normalized_url);

		// Final patch to remove potential trailing artifacts
		$normalized_url = rtrim_with_replace($normalized_url, '/?', '/');

		return $normalized_url;
	}
}

if (!function_exists('is_working')) {
	function is_working($url)
	{
		global $absolutes_urls_that_is_not_found;
		global $absolutes_urls_that_is_working;

		if (!isset($absolutes_urls_that_is_not_found)) {
			$absolutes_urls_that_is_not_found = array();
		}
		if (!isset($absolutes_urls_that_is_working)) {
			$absolutes_urls_that_is_working = array();
		}

		// patch url to not be confused wih the 302 redirect
		// $url = patch_url_with_trailing_slash($url); δεν ειναι καλό, καλήτερα ο έλενχος να γίνετε πριν γιατί υπάρχουν και url που λειτουργούν έτσι

		// is url is compatible
		if (!is_full_url($url)) {
			// το απενεργοποίησα γιατί δεν χρειάζεστε να στέλνει
			// trigger_error('url ' . $url . " is not compatible http url");
			return false;
		}

		// if one time not found, then always not found, super clever
		elseif (in_array($url, $absolutes_urls_that_is_not_found)) {
			return false;
		}

		// if one time found, then always found, super clever
		elseif (in_array($url, $absolutes_urls_that_is_working)) {
			return true;  // this is true and not false
		}

		// check url status with curl
		elseif (!function_exists('curl_init')) {
			// ειδοποίηση
			trigger_error('curl is not installed');

			// ας βγάζει true γιατί δεν ξέρουμε αν θα υπάρχει
			return true;
		} else {
			$handle = curl_init($url);
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);  // quick fix for ssl
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_NOBODY, true);

			// set this 2 sec don't spend one life here
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 4);

			// set a user again to pass the error 403 Forbidden note: always at the last option
			// $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
			$user_agent = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3B48b Safari/419.3';  // please use mobile to emulate google crawler
			curl_setopt($handle, CURLOPT_USERAGENT, $user_agent);

			curl_exec($handle);
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			curl_close($handle);
			if ($httpCode >= 200 && $httpCode < 300) {
				// working correctly
				$absolutes_urls_that_is_working[] = $url;
				return true;
			} else {
				// not found
				$absolutes_urls_that_is_not_found[] = $url;
				return false;
			}
		}
	}
}

if (!function_exists('update_url_params')) {
	function update_url_params($new_params, $base_url = false, $use_only_new_params = false, $custom_url = null)
	{
		// Get the current URL or use the custom URL if provided
		$current_url = $custom_url ?? $_SERVER['REQUEST_URI'];

		// Parse the current URL
		$parsed_url = parse_url($current_url);

		// Determine the scheme
		$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

		// Use the provided base URL if given, otherwise use the current URL's base
		if ($base_url !== false) {
			$parsed_base_url = parse_url($base_url);
			$scheme = isset($parsed_base_url['scheme']) ? $parsed_base_url['scheme'] : $scheme;
			$host = isset($parsed_base_url['host']) ? $parsed_base_url['host'] : $_SERVER['HTTP_HOST'];
			$port = isset($parsed_base_url['port']) ? ':' . $parsed_base_url['port'] : '';
			$path = isset($parsed_base_url['path']) ? $parsed_base_url['path'] : '/';
		} else {
			$host = isset($parsed_url['host']) ? $parsed_url['host'] : $_SERVER['HTTP_HOST'];
			$port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
			$path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
		}

		// If a custom URL is provided, remove the host part
		if ($custom_url !== null) {
			$host = $_SERVER['HTTP_HOST'];
			$port = '';
			if (isset($path) && substr($path, 0, 1) !== '/') {
				$path = '/' . $path;
			}
		}

		// Parse the query string into an associative array
		parse_str($parsed_url['query'] ?? '', $current_params);

		// If use_only_new_params is true, start with an empty array
		if ($use_only_new_params) {
			$current_params = [];
		}

		// Update the parameters based on the new_params array
		foreach ($new_params as $key => $value) {
			if ($value === null || $value === false) {
				unset($current_params[$key]);
			} else {
				$current_params[$key] = $value;
			}
		}

		// Build the new query string
		$new_query_string = http_build_query($current_params);

		// Preserve the fragment (#hash) from the original URL
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

		// Construct the new URL
		$new_url = $scheme . '://' . $host . $port . $path . ($new_query_string ? '?' . $new_query_string : '') . $fragment;

		return $new_url;
	}
}

if (!function_exists('update_url_params_original')) {
	function update_url_params_original($array = array(), $new_url = false, $clean_current_params = false)
	{

		##############################################
		# MODIFY URL SET PARAMETERS                  #
		# AS SEEN ON ARRAY                           #
		# SET IF WANTS TO SPECIFY A URL PATH         #
		##############################################

		$updated_params = array();

		//get current params
		if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], "?")) {

			$string = explode('?', rawurldecode($_SERVER['REQUEST_URI']));

			//part 1 k 2
			parse_str($string[1], $current_params);
		} elseif (isset($_SERVER['REQUEST_URI'])) {

			//part 0
			$string[0] = rawurldecode($_SERVER['REQUEST_URI']);
		}

		//parameters handler
		if (isset($string[1]) && $clean_current_params == false) { //1 was 2 ????

			//delete empty params
			foreach ($current_params as $current_param => $value) {

				if ($value == null) {

					$string[1] = str_replace($current_param . "=", '', $string[1]);

					//fallback delete with =
					$string[1] = str_replace($current_param, '', $string[1]);

					//apply clean filters
					if (substr($string[1], -1, 1) == '&') {

						$string[1] = rtrim($string[1], "&");
					} elseif (substr($string[1], 0, 1) == '&') {

						$string[1] = ltrim($string[1], "&");
					} elseif (strpos($string[1], '&&') !== false) {

						$string[1] = str_replace('&&', '&', $string[1]);
					}
				}
			}

			//update params, please not empty the array, we want the null parameters here
			foreach ($array as $param => $value) {

				//please decode always urls that are input on program to fix bugs [NEW]
				if (isset($current_params[$param]) && $value != null) {

					$previous_value = $current_params[$param];
					$string[1] = str_replace($param . '=' . $previous_value, $param . '=' . $value, $string[1]);
					array_push($updated_params, $param);
				} elseif (isset($current_params[$param]) && $value == null) {

					//get the query
					$string[1] = str_replace($param . "=" . $current_params[$param], '', $string[1]);

					//apply clean filters
					if (substr($string[1], -1, 1) == '&') {

						$string[1] = rtrim($string[1], "&");
					} elseif (substr($string[1], 0, 1) == '&') {

						$string[1] = ltrim($string[1], "&");
					} elseif (strpos($string[1], '&&') !== false) {

						$string[1] = str_replace('&&', '&', $string[1]);
					}

					array_push($updated_params, $param);
				}
			}
		} else {

			//clean url if clean is true
			$string[1] = null;
		}

		//set new params
		foreach ($array as $param => $value) {

			if (!in_array($param, $updated_params)) {

				if ($value != null) {

					$string[1] .= '&' . $param . '=' . $value;
				}
			}
		}

		//if i don't have a new page
		if ($new_url == false) {

			if ($string[1] != null) {

				$var = site_url_des() . $string[0] . '?' . $string[1];
				$var = str_replace('?&', '?', $var);
				return $var;
			} else {

				if (str_starts_with($string[0], "/")) {
					return site_url_des() . $string[0];
				} else {
					return $string[0];
				}
			}
		}

		//if i have a new page, push new page and request after ?...
		else {

			if ($string[1] != null) {

				//fix and return
				$var = $new_url . '?' . $string[1];
				$var = str_replace('?&', '?', $var);

				//todo//fixme// Θα μπορούσε να έχει html, αλλά είναι πιο πιθανό να είναι php αν έχει queries
				$var = str_replace("/?", "/index.php?", $var);

				//fix and return
				if (str_starts_with($var, "/")) {
					return site_url_des() . $var;
				} else {
					return $var;
				}

			} else {

				//fix and return
				if (str_starts_with($new_url, "/")) {
					return site_url_des() . $new_url;
				} else {
					return $new_url;
				}
			}
		}

		#################################################################
		# testing function outside of function
		# $array = array("cx"=>2, "cy"=>2, "lx"=>2, "ly"=>2, 'mode'=>null);
		# echo update_url_params($array, site_url_des() . '/app/listing-index.php');
		# $array = array("cx"=>2, "cy"=>2, "lx"=>2, "ly"=>2);
		# echo update_url_params($array);
		##################################################################
	}
}

if (!function_exists('get_current_url')) {
	function get_current_url()
	{
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

		if (isset($_SERVER['REQUEST_URI'])) {
			$actual_link .= $_SERVER['REQUEST_URI'];
		}
		return $actual_link;
	}
}

if (!function_exists('get_current_url_no_query')) {
	function get_current_url_no_query()
	{
		$url = get_current_url();
		$url = explode('?', $url);
		$url = $url[0];
		return $url;
	}
}

if (!function_exists('get_current_request')) {
	function get_current_request()
	{
		return $_SERVER['REQUEST_URI'];
	}
}

if (!function_exists('get_current_url_encoded')) {
	function get_current_url_encoded()
	{
		return rawurlencode(get_current_url());
	}
}

if (!function_exists('remove_url_params')) {
	function remove_url_params($url, $param)
	{
		$url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
		$url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
		return $url;
	}
}

if (!function_exists('remove_url_params_all')) {
	function remove_url_params_all($url)
	{
		$url = explode('?', $url);
		$url = $url[0];
		return $url;
	}
}

if (!function_exists('remove_url_hostname')) {
	function remove_url_hostname($url = false)
	{
		$components = parse_url($url);
		$url = $components['path'] . '?'
			. $components['query'] . '#'
			. $components['fragment'];
		return $url;
	}
}

if (!function_exists('get_file_path_from_url')) {
	function get_file_path_from_url($custom_url = false)
	{
		if ($custom_url == false) {
			$file = str_replace(site_url_des() . '/', '', get_current_url_no_query());
		} else {
			$file = str_replace(site_url_des() . '/', '', $custom_url);
		}

		return $file;
	}
}

if (!function_exists('is_full_url')) {
	function is_full_url($url)
	{
		// Custom implementation of str_contains for PHP 5 compatibility
		if (strpos($url, "https://") !== false || strpos($url, "http://") !== false || substr($url, 0, 2) === "//") {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('is_typical_url')) {
	function is_typical_url($url)
	{
		$ext = get_extension($url);
		if ($ext != null) {
			return true;
		} else {
			// Custom implementation of str_ends_with for PHP 5 compatibility
			$length = strlen('/');
			if (substr($url, -$length) === '/') {
				return true;
			} else {
				return false;
			}
		}
	}
}

if (!function_exists('get_domain_from_url')) {
	function get_domain_from_url($url = false)
	{
		$url = str_replace('https://www.', '', $url);
		$url = str_replace('http://www.', '', $url);
		$url = str_replace('https://', '', $url);
		$url = str_replace('http://', '', $url);

		// new for relative urls
		$url = str_replace('../../../../../', '', $url);
		$url = str_replace('../../../../', '', $url);
		$url = str_replace('../../../', '', $url);
		$url = str_replace('../../', '', $url);
		$url = str_replace('../', '', $url);
		if ($url == true) {
			$url = str_read_until($url, '/');
			return $url;
		} else {
			if (is_cli()) {
				return basename($_SERVER['DOCUMENT_ROOT']);
			} else {
				return $_SERVER['HTTP_HOST'];
			}
		}
	}
}

if (!function_exists('rel2abs')) {
	function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') {
			return ($rel);
		}

		/* Ensure $rel is not null or empty */
		if (empty($rel)) {
			return $base;
		}

		/* queries and anchors */
		if ($rel[0] == '#' || $rel[0] == '?') {
			return ($base . $rel);
		}

		/* parse base URL and convert to local variables: $scheme, $host, $path, $port, $user, $pass */
		$parsed_base = parse_url($base);

		/* remove query from base if present */
		if (isset($parsed_base['query'])) {
			unset($parsed_base['query']);
		}

		/* remove non-directory element from path */
		$path = preg_replace('#/[^/]*$#', '', $parsed_base['path']);

		/* destroy path if relative url points to root */
		if ($rel[0] == '/') {
			$path = '';
		}

		/* build dirty absolute URL */
		$abs = '';

		/* do we have a user in our URL? */
		if (isset($parsed_base['user'])) {
			$abs .= $parsed_base['user'];

			/* password too? */
			if (isset($parsed_base['pass'])) {
				$abs .= ':' . $parsed_base['pass'];
			}

			$abs .= '@';
		}

		$abs .= $parsed_base['host'];

		/* did somebody sneak in a port? */
		if (isset($parsed_base['port'])) {
			$abs .= ':' . $parsed_base['port'];
		}

		$abs .= $path . '/' . $rel;

		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {
		}

		/* absolute URL is ready! */
		return ($parsed_base['scheme'] . '://' . $abs);
	}
}

if (!function_exists('str_replace_sources_in_html')) {
	function str_replace_sources_in_html($url, $new_url, $html)
	{
		$html = str_ireplace('src="' . $url . '"', 'src="' . $new_url . '"', $html);
		$html = str_ireplace("src='" . $url . "'", "src='" . $new_url . "'", $html);
		$html = str_ireplace('action="' . $url . '"', 'action="' . $new_url . '"', $html);
		$html = str_ireplace("action='" . $url . "'", "action='" . $new_url . "'", $html);
		$html = str_ireplace('url(' . $url . ')', 'url(' . $new_url . ')', $html);
		$html = str_ireplace('url("' . $url . '")', 'url("' . $new_url . '")', $html);
		$html = str_ireplace("url('" . $url . "')", "url('" . $new_url . "')", $html);
		$html = str_ireplace('background="' . $url . '"', 'background="' . $new_url . '"', $html);
		$html = str_ireplace("background='" . $url . "'", "background='" . $new_url . "'", $html);
		$html = str_ireplace('content="' . $url . '"', 'content="' . $new_url . '"', $html);
		$html = str_ireplace("content='" . $url . "'", "content='" . $new_url . "'", $html);
		return $html;
	}
}

if (!function_exists('get_all_sources_from_html')) {
	function get_all_sources_from_html($html)
	{
		$html = str_ireplace('src ="', '<a src"=', $html);
		$html = str_ireplace('src = "', '<a src="', $html);
		$html = str_ireplace('src= "', '<a src="', $html);

		$html = str_ireplace("src ='", "<a src'=", $html);
		$html = str_ireplace("src = '", "<a src='", $html);
		$html = str_ireplace("src= '", "<a src='", $html);
		$html = str_ireplace('<a  src', '<a src', $html);

		$html = str_ireplace('src="', 'src="', $html);

		$urls_1 = str_between_all($html, 'src="', '"');
		$urls_2 = str_between_all($html, "src='", "'");
		$urls_3 = str_between_all($html, 'url("', '")');
		$urls_4 = str_between_all($html, "url('", "')");
		$urls_5 = str_between_all($html, 'url(', ')');
		$urls_6 = str_between_all($html, 'background="', '"');
		$urls_7 = str_between_all($html, "background='", "'");
		$urls_8 = str_between_all($html, 'content="', '"');
		$urls_9 = str_between_all($html, "content='", "'");

		$urls = array_merge($urls_1, $urls_2, $urls_3, $urls_4, $urls_5, $urls_6, $urls_7, $urls_8, $urls_9);
		$urls = array_remove_empty_values($urls);
		$urls = array_remove_duplicate_values($urls);

		$valid_image_extensions = [
			'jpg',
			'jpeg',
			'png',
			'gif',
			'bmp',
			'webp',
			'svg',
			'tiff',
			'tif',
			'ico',
			'heic',
			'heif',
			'jfif',
			'pjpeg',
			'pjp'
		];

		return array_filter($urls, function ($url) use ($valid_image_extensions) {
			$extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
			return in_array($extension, $valid_image_extensions);
		});
	}
}

if (!function_exists('str_replace_links_in_html')) {
	function str_replace_links_in_html($url, $new_url, $html, $format_options = false)
	{
		if (function_exists('read_canonical')) {
			$canonical_url = read_canonical($html);
		}

		$html = str_ireplace('href="' . $url . '"', 'href="' . $new_url . '"', $html);
		$html = str_ireplace('href="' . $url . "'", 'href="' . $new_url . "'", $html);

		if ($format_options == true) {
			$html = str_ireplace('value="' . $url . '"', 'value="' . $new_url . '"', $html);
			$html = str_ireplace("value='" . $url . "'", "value='" . $new_url . "'", $html);
		}

		if (function_exists('update_canonical') && isset($canonical_url)) {
			$html = update_canonical($html, $canonical_url);
		}

		return $html;
	}
}

if (!function_exists('get_all_links_from_html')) {
	function get_all_links_from_html($html)
	{
		$html = str_ireplace('.html "', '.html"', $html);
		$html = str_ireplace(".html '", '.html"', $html);
		$html = str_ireplace('href ="', '<a href"=', $html);
		$html = str_ireplace('href = "', '<a href="', $html);
		$html = str_ireplace('href= "', '<a href="', $html);

		$html = str_ireplace("href ='", "<a href'=", $html);
		$html = str_ireplace("href = '", "<a href='", $html);
		$html = str_ireplace("href= '", "<a href='", $html);
		$html = str_ireplace('<a  href', '<a href', $html);

		$html = str_ireplace('href="', 'href="', $html);
		$html = str_ireplace('value="', 'value="', $html);

		$urls_1 = str_between_all($html, 'href="', '"');
		$urls_2 = str_between_all($html, "href='", "'");
		$urls_3 = str_between_all($html, "value='", "'");
		$urls_4 = str_between_all($html, 'value="', '"');

		$urls = array_merge($urls_1, $urls_2, $urls_3, $urls_4);
		$urls = array_remove_empty_values($urls);
		$urls = array_remove_duplicate_values($urls);

		return array_filter($urls, function ($url) use ($html) {
			return strpos($html, '<link rel="canonical" href="' . $url . '"') === false;
		});
	}
}

if (!function_exists('rel2abs_in_html')) {
	function rel2abs_in_html($html, $file_or_url)
	{
		$file_or_url = str_ireplace('www.', '', $file_or_url);

		$html = str_replace('content: (', 'content: url(', $html);
		$html = str_replace('background-image: (', 'background-image: url(', $html);

		if (!is_full_url($file_or_url)) {
			$url_original = path2url($file_or_url);
		} else {
			$url_original = $file_or_url;
		}

		$urls_from_html = get_all_sources_from_html($html);
		foreach ($urls_from_html as $url_from_html) {
			if (!is_full_url($url_from_html)) {
				$url_from_html_abs = rel2abs($url_from_html, $url_original);
				$html = str_replace_sources_in_html($url_from_html, $url_from_html_abs, $html);
			}
		}

		$urls_from_html = get_all_links_from_html($html);
		foreach ($urls_from_html as $url_from_html) {
			if (!is_full_url($url_from_html)) {
				$url_from_html_abs = rel2abs($url_from_html, $url_original);
				$html = str_replace_links_in_html($url_from_html, $url_from_html_abs, $html);
			}
		}

		return $html;
	}
}

if (!function_exists('is_valid_domain_name')) {
	function is_valid_domain_name($domain_name)
	{
		return (preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domain_name) &&  // valid chars check
			preg_match('/^.{1,253}$/', $domain_name) &&  // overall length check
			preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domain_name) &&  // length of each label
			preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain_name));
	}
}

if (!function_exists('patch_url_with_index')) {
	function patch_url_with_index($url)
	{
		// Custom implementation of str_ends_with for PHP 5 compatibility
		$length = strlen('/');
		if (substr($url, -$length) === '/') {
			$url = $url . 'index.php';
			if (file_exists($url)) {
				return $url;
			} else {
				return str_replace('.php', '.html', $url);
			}
		} else {
			return $url;
		}
	}
}

if (!function_exists('site_host')) {
	function site_host()
	{
		$host = site_url_des();

		// trim http and https from there
		$host = str_replace('http://', '', $host);
		$host = str_replace('https://', '', $host);
		return strtolower($host);
	}
}

if (!function_exists('export_domain_from_path')) {
	function export_domain_from_path($path)
	{
		// search path root path
		$path = normalize_path($path);
		$domain = null;  // "example.org";
		$parts = explode('/', $path);
		foreach ($parts as $part) {
			if (is_valid_domain_name($part)) {
				$domain = $part;
				break;
			}
		}

		if ($domain == null) {
			// if not any domain found in the path then use this
			$domain = $_SERVER['HTTP_HOST'];
		}
		return $domain;
	}
}

if (!function_exists('path2url')) {
	function path2url($path, $root_path = false, $root_url = false, $use_production_url = false)
	{

		// ξεκινάει εδώ
		$path = normalize_path($path);

		if ($root_path == false || $root_url == false) {
			if (is_cli()) {
				$domain = export_domain_from_path($path);
				if ($domain) {
					// find the position of the domain folder in the path and use it as root
					$normalized = normalize_path($path);
					$domain_pos = strpos($normalized, '/' . $domain . '/');
					if ($domain_pos !== false) {
						$root_path = substr($normalized, 0, $domain_pos + strlen('/' . $domain));
					} else {
						$root_path = normalize_path(dirname(__DIR__));
					}
					$root_url = 'https://' . $domain;
				} else {
					$domain = basename(dirname(dirname(__FILE__)));
					$root_path = normalize_path(dirname(__DIR__));
					$root_url = 'https://' . $domain;
				}
			} else {
				$root_path = normalize_path($_SERVER['DOCUMENT_ROOT']);
				$root_url = site_url_des();
			}
		}

		// finally make the url
		$root_path = normalize_path($root_path);
		$root_path = rtrim($root_path, '/');
		if ($path == $root_path) {
			$url = $root_url;
		} elseif (str_starts_with($path, $root_path . '/')) {
			$url = $root_url . substr($path, strlen($root_path));
		} else {
			$url = $path;
		}

		// Use the directory URL for an actual index.html file.
		if (strtolower(basename($path)) === 'index.html') {
			$url = rtrim(dirname($url), '/') . '/';
		}

		// patch url with a slash only if is dir
		if (str_contains($path, "/*")) {

			//dont check is dir for *...
			return $url;
		}

		// patch url with a slash only if is dir
		if (is_dir($path)) {
			$length = strlen('/');
			if (substr($url, -$length) !== '/') {
				$url .= '/';
			}
		}

		//final fix allways in https is if cli
		if (is_cli()) {
			$url = str_replace("http://", "https://", $url);
		}
		return $url;
	}
}

/////////////// PAGINATION URLS FROM KOST IS NOW HERE
if (!function_exists('setup_pagination_variables')) {
	function setup_pagination_variables($prefix = false, $step = 10)
	{

		//condision key
		if ($prefix == true) {

			if (isset($_GET[$prefix . '-step'])) {
				$step = $_GET[$prefix . '-step'];
			}

			//get min and max
			global $min;
			global $max;
			if (isset($_GET[$prefix . '-min'])) {

				$min = $_GET[$prefix . '-min'];
			} else {
				$min = 0;
			}
			if (isset($_GET[$prefix . '-max'])) {

				$max = $_GET[$prefix . '-max'];
			} else {

				$max = 10;
			}

			//get current page
			global $current_page;
			$current_page = $max / $step;
		} else {

			if (isset($_GET['step'])) {
				$step = $_GET['step'];
			}

			//get min and max
			global $min;
			global $max;
			if (isset($_GET['min'])) {

				$min = $_GET['min'];
			} else {
				$min = 0;
			}
			if (isset($_GET['max'])) {

				$max = $_GET['max'];
			} else {

				$max = $step;
			}

			//get current page
			global $current_page;
			$current_page = $max / $step;
		}
	}
}

if (!function_exists('reset_pagination_variables')) {
	function reset_pagination_variables()
	{
		//get min and max
		global $min;
		global $max;
		$min = 0; //pls not modify the get paramets
		$max = 10; //default is 10

		//get current page
		global $current_page;
		$current_page = 1;
	}
}

if (!function_exists('is_running_file_php_or_html')) {
	function is_running_file_php_or_html()
	{

		$extensions = array("html", "htm", "php", "phtml");
		$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if (in_array($ext, $extensions)) {

			return true;
		} else {

			return false;
		}
	}
}

/**
 * Checks if the running file is static (HTML or PHP).
 *
 * @return bool Returns true if the running file is static, false otherwise.
 */
if (!function_exists('is_running_file_static')) {
	function is_running_file_static()
	{
		$script_name = parse_url($_SERVER["SCRIPT_FILENAME"], PHP_URL_PATH);
		$script_name = str_replace("\\", "/", $script_name);

		// Check if the file exists
		if (!file_exists($script_name)) {
			return false; // File does not exist, we process now for rewriting
		}

		// Check by file extension first
		$static_extensions = array(
			'txt',
			'js',
			'css',
			'ico',
			'jpg',
			'jpeg',
			'png',
			'gif',
			'svg',
			'woff',
			'woff2',
			'ttf',
			'eot',
			'otf',
			'mp4',
			'webm',
			'ogg',
			'mp3',
			'wav',
			'pdf',
			'zip',
			'rar',
			'bmp',
			'tiff',
			'webp',
			'flac',
			'avi',
			'mov',
			'mkv',
			'mpg',
			'mpeg',
			'txt',
			'md',
			'log',
			'csv',
			'json',
			'xml',
			'map',
			'jfif',
		);
		$file_extension = pathinfo($script_name, PATHINFO_EXTENSION);
		if (in_array(strtolower($file_extension), $static_extensions)) {
			return true; // File is static based on extension
		}

		// Check if the finfo class is available
		if (!class_exists('finfo')) {
			// Fallback or error handling if finfo is not available
			return false;
		}

		// Create a new finfo object for MIME type detection
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		if (!$finfo) {
			// finfo could not be initialized
			return false;
		}

		// Get the MIME type of the file
		$mime_type = $finfo->file($script_name);
		if (!$mime_type) {
			// Could not determine MIME type
			return false;
		}

		// Define MIME types for PHP, HTML, XML, and JavaScript files
		$non_static_mime_types = array(
			'text/html',               // HTML files
			'application/xhtml+xml',   // XHTML files
			'application/xml',         // XML files
			'text/xml',                // XML files
			'application/x-httpd-php', // PHP files
			'text/x-php',              // PHP files
		);

		// Check if the MIME type is one of the non-static types
		if (in_array($mime_type, $non_static_mime_types)) {
			return false; // File is not static
		}

		return false; // File is static
	}
}

//activate here
if (is_running_file_static() && file_exists($_SERVER["SCRIPT_FILENAME"])) {

	header("Content-Type: " . mime_content_type_with_special_case($_SERVER["SCRIPT_FILENAME"]));
	header('Content-Disposition: inline; filename="' . basename($_SERVER["SCRIPT_FILENAME"]) . '"');
	readfile($_SERVER["SCRIPT_FILENAME"]);
	exit();
}

/**
 * Recursively strip slashes from quotes in input data.
 *
 * @param mixed $data The input data to process.
 * @return mixed The processed data.
 */
if (!function_exists('strip_escapted_quotes')) {
	function strip_escapted_quotes($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = strip_escapted_quotes($value);
			}
		} elseif (is_string($data)) {
			// Strip slashes from quotes
			$data = str_replace(array('\"', "\'"), array('"', "'"), $data);
		}
		return $data;
	}
}

if (!function_exists('is_running_file_html')) {
	function is_running_file_html()
	{

		$extensions = array("html", "htm");
		$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if (in_array($ext, $extensions)) {

			return true;
		} else {

			return false;
		}
	}
}

if (!function_exists('is_running_file_php')) {
	function is_running_file_php()
	{

		$extensions = array("php", "phtml");
		$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		if (in_array($ext, $extensions)) {

			return true;
		} else {

			return false;
		}
	}
}

if (!function_exists('get_script_name_via_request')) {
	function get_script_name_via_request()
	{

		//new: we get that now from script filename
		$originalPath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $_SERVER["SCRIPT_FILENAME"]);
		return $originalPath;
	}
}

if (!function_exists('get_script_filename_via_request')) {
	function get_script_filename_via_request()
	{
		$originalURL = $_SERVER['REQUEST_URI'];
		$originalPath = parse_url($originalURL, PHP_URL_PATH);
		$originalPath = $_SERVER['DOCUMENT_ROOT'] . $originalPath; //works only when routes in on common folder
		$originalPath = str_replace("\\", "/", $originalPath);
		$originalPath = patch_url_with_index_2($originalPath);
		return $originalPath;
	}
}

if (!function_exists('validate_file_path')) {
	function validate_file_path($path)
	{
		try {
			// Normalize the path to prevent directory traversal
			$realPath = realpath($path);

			// If realpath returns false, the path is invalid
			if ($realPath === false) {
				return false;
			}

			// Get open_basedir setting
			$openBasedir = ini_get('open_basedir');

			// If open_basedir is not set, just check if file exists
			if (empty($openBasedir)) {
				return file_exists($realPath);
			}

			// Split open_basedir into array of allowed directories
			$allowedDirs = explode(PATH_SEPARATOR, $openBasedir);

			// Check if file path is within any allowed directory
			foreach ($allowedDirs as $dir) {
				if (strpos($realPath, rtrim($dir, DIRECTORY_SEPARATOR)) === 0) {
					return file_exists($realPath);
				}
			}

			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}

if (!function_exists('patch_url_with_index_2')) {
	function patch_url_with_index_2($request)
	{
		$parsedUrl = parse_url($request);

		// append 'index.html' if the URL ends with a slash
		if (isset($parsedUrl['path']) && !preg_match('/\.\w+$/', $parsedUrl['path'])) {

			// If url contains a request with a / then append index.html before the request /
			if (substr($parsedUrl['path'], -1) === '/') {

				if (strpos($request, '?') !== false) {
					$request = str_replace('/?', '/index.html?', $request);
				} else {
					$request .= 'index.html';
				}
			}
		}

		// If it's not a URL and a corresponding .php file exists, replace .html with .php
		if (validate_file_path(str_replace(".html", ".php", $request))) {
			$request = str_replace("/index.html", "/index.php", $request);
		}

		return $request;
	}
}

if (!function_exists('setup_environment_for_an_other_url')) {
	function setup_environment_for_an_other_url($url, $url_for_parsing_queries)
	{

		global $_SERVER;

		//fix index if is missing
		$url = patch_url_with_index_2($url);
		$url_without_query = patch_url_with_index_2($url_for_parsing_queries);

		//find current file early without path2url
		$parsed_url = parse_url($url);
		$protocol = (isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'http') . '://';
		$host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$url_without_query = $protocol . $host . $path;
		$current_file = str_replace($protocol . $host, $_SERVER['DOCUMENT_ROOT'], $url_without_query);

		// Check if the path is index.php and not found, try index.html
		if (basename($current_file) === 'index.php' && !file_exists($current_file)) {
			$current_file = str_replace('index.php', 'index.html', $current_file);
		}

		$_SERVER['SCRIPT_FILENAME'] = $current_file;
		$_SERVER['SCRIPT_NAME'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $current_file);
		$_SERVER['PHP_SELF'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $current_file);
		$_SERVER['REQUEST_URI'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $current_file);
		$_SERVER["QUERY_STRING"] = parse_url($url_for_parsing_queries, PHP_URL_QUERY);

		//if ends with ? then delete ?
		if (substr($_SERVER['REQUEST_URI'], -1, 1) == '?') {
			$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], "?");
		}

		//fix request uri if query is not null
		if ($_SERVER["QUERY_STRING"] != "") {
			$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] . "?" . $_SERVER["QUERY_STRING"];
		}
		$_SERVER['REQUEST_URI'] = patch_url_with_index_2($_SERVER['REQUEST_URI']);

		//place request parameters with the parameters from query string along with existing ones
		$request_parameters = parse_url($url_for_parsing_queries, PHP_URL_QUERY);
		if ($request_parameters != null) {

			parse_str($request_parameters, $request_parameters_array);

			foreach ($request_parameters_array as $key => $value) {
				$_REQUEST[$key] = $value;
			}
			parse_str($request_parameters, $_GET);
		}

		chdir(dirname($current_file));
	}
}

if (!function_exists('setup_environment_for_cli')) {
	function setup_environment_for_cli()
	{

		global $_SERVER;

		//emulate server variables on cli, this for document root
		if (file_exists("../../../../../../../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "../../../../../../.."; //was realpath here
		} elseif (file_exists("../../../../../../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "../../../../../..";
		} elseif (file_exists("../../../../../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "../../../../..";
		} elseif (file_exists("../../../../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "../../../..";
		} elseif (file_exists("../../../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "../../..";
		} elseif (file_exists("../../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "../..";
		} elseif (file_exists("../.htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = "..";
		} elseif (file_exists(".htaccess")) {
			$_SERVER['DOCUMENT_ROOT'] = ".";
		} else {

			//αν δεν υπάρχει το htacess αυτή θα ειναι η τελευταία του επιλογή
			$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(realpath(__FILE__)));
		}

		//emulate server variables on cli, this is the fake server
		$_SERVER['DOCUMENT_ROOT'] = realpath($_SERVER['DOCUMENT_ROOT']);
		$_SERVER['HTTP_HOST'] = basename($_SERVER['DOCUMENT_ROOT']);
		$_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
		$_SERVER['SERVER_NAME'] = basename($_SERVER['DOCUMENT_ROOT']);
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['REQUEST_SCHEME'] = 'https';
		$_SERVER['SERVER_PORT'] = "443"; //was 80
		$_SERVER['REQUEST_METHOD'] = "GET";
		$_SERVER['HTTPS'] = 'on';
		$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
		$_SERVER['HTTP_USER_AGENT'] = 'cli';
		$_SERVER['HTTP_SEC_FETCH_DEST'] = 'cli';
		$_SERVER['HTTP_REFERER'] = null;
		$_SERVER['REQUEST_URI'] = str_ireplace($_SERVER['HTTP_HOST'], '', $_SERVER['PHP_SELF']);
		//todo: query string is missing also push get variables inside request uri and query string

		//normalize this variables
		$_SERVER['REQUEST_URI'] = str_replace("\\", "/", $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = str_replace("//", "/", $_SERVER['REQUEST_URI']);
	}
}

if (!function_exists('setup_environment_for_browser')) {
	function setup_environment_for_browser()
	{

		global $_SERVER;

		//setup rooting and replace server variable like is direcly calling the script
		//we removed request uri patch because was casing problem with wordpress - plus is not a good practice to change the url...
		$_SERVER["REQUEST_URI"] = str_replace("/favicon.ico/", "/favicon.ico", $_SERVER["REQUEST_URI"]); //patch for google chrome
		$_SERVER["SCRIPT_FILENAME"] = get_script_filename_via_request(); //push script name from url
		$_SERVER["PHP_SELF"] = get_script_name_via_request(); //push script name from url
		$_SERVER["SCRIPT_NAME"] = get_script_name_via_request(); //push script from from url absolute path
		$_SERVER["QUERY_STRING"] = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY);

		//change current directory to the directory of the script
		if (is_dir(dirname($_SERVER["SCRIPT_FILENAME"]))) {
			chdir(dirname($_SERVER["SCRIPT_FILENAME"]));
		}
	}
}

if (!function_exists('strip_escaped_slashes')) {
	function strip_escaped_slashes(array $inputArray)
	{
		foreach ($inputArray as $key => $value) {
			if (is_string($value)) {
				$inputArray[$key] = str_replace('\\\\', '\\', $value);
			}
		}
		return $inputArray;
	}
}

/**
 * Checks if the current file is included.
 *
 * @return bool Returns true if the current file is included, false otherwise.
 */
if (!function_exists('is_file_included_and_its_alone')) {
	function is_file_included_and_its_alone()
	{
		$included_files = get_included_files();
		$c = count($included_files);

		if ($c == 1 && in_array(__FILE__, $included_files)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('mime_content_type_with_special_case')) {
	function mime_content_type_with_special_case($filename)
	{
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		// Define special cases
		switch ($extension) {
			// Web essentials
			case 'css':
				return 'text/css';
			case 'js':
				return 'text/javascript';

			// Font files
			case 'ttf':
				return 'font/ttf';
			case 'otf':
				return 'font/otf';
			case 'woff':
				return 'font/woff';
			case 'woff2':
				return 'font/woff2';
			case 'eot':
				return 'application/vnd.ms-fontobject';

			// Default fallback
			default:
				return mime_content_type($filename);
		}
	}
}

////////////////////// IMAGE FUNCTIONS //////////////////////

if (!function_exists('read_image_type')) {
	function read_image_type($path)
	{
		$image_info = getImageSize($path);
		switch ($image_info['mime']) {
			case 'image/gif':
				return 'gif';
			case 'image/jpeg':
				return 'jpg';
			case 'image/png':
				return 'png';
			default:
				return false;
		}
	}
}

if (!function_exists('image_resize_des')) {
	function image_resize_des($source, $w, $h, $autocrop = false, $destination = null)
	{
		// Increase memory limit
		ini_set('memory_limit', '256M');

		//note: this function will not set width or height to auto mode so will not shrink

		//set dimensions
		$x = array();
		$y = array();

		//requested dimensions
		$x[2] = $w;
		$y[2] = $h;

		//get original dimensions
		list($x[1], $y[1]) = getimagesize($source);

		//συντελεστής
		$c = $x[1] / $y[1];

		//if no crop
		if ($autocrop == true) {
			if ($x[2] / $y[2] > $c) {
				$x[3] = $y[2] * $c;
				$y[3] = $y[2];
			} else {
				$x[3] = $x[2];
				$y[3] = $x[2] / $c;
			}
		} else {
			$x[3] = $x[2];
			$y[3] = $x[2];
		}

		//select file extension
		$read = read_image_type($source);
		if ($read == 'png') {
			$src = imagecreatefrompng($source);
		} elseif ($read == 'jpg') {
			$src = imagecreatefromjpeg($source);
		} elseif ($read == 'gif') {
			$src = imagecreatefromgif($source);
		} else {
			$src = imagecreatefromjpeg($source);
		}

		$dst = imagecreatetruecolor($x[3], $y[3]); //this function save the image
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $x[3], $y[3], $x[1], $y[1]);

		//save the image and destroy resources
		if ($read == 'jpg') {
			if ($destination != null) {
				imagejpeg($dst, $destination, 100);
			}
			imagedestroy($src);
			imagedestroy($dst);
			return $dst;
		} elseif ($read == 'png') {
			if ($destination != null) {
				imagepng($dst, $destination);
			}
			imagedestroy($src);
			imagedestroy($dst);
			return $dst;
		} elseif ($read == 'gif') {
			if ($destination != null) {
				imagegif($dst, $destination);
			}
			imagedestroy($src);
			imagedestroy($dst);
			return $dst;
		} else {
			imagedestroy($src);
			imagedestroy($dst);
			return false;
		}
	}
}

if (!function_exists('png2jpg')) {
	function png2jpg($originalFile, $outputFile, $quality)
	{
		$image = imagecreatefrompng($originalFile);
		imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
	}
}

if (!function_exists('jpg2jpg')) {
	function jpg2jpg($originalFile, $outputFile, $quality)
	{

		$image = imagecreatefromjpeg($originalFile);
		imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
	}
}

if (!function_exists('crop_image')) {
	function crop_image($source, $w, $h, $destination = null)
	{

		//set dimensions
		$x = array();
		$y = array();

		//requested dimensions
		$x[2] = $w;
		$y[2] = $h;

		//get original dimensions
		list($x[1], $y[1]) = getimagesize($source);

		//factor
		$c = $x[1] / $y[1];

		//requested crop
		if ($x[1] > $y[1]) {
			$x[1] = ceil($x[1] - ($x[1] * abs($c - $x[2] / $y[2])));
		} else {
			$y[1] = ceil($y[1] - ($y[1] * abs($c - $x[2] / $y[2])));
		}

		//save the new dimensions
		$x[3] = $x[2];
		$y[3] = $y[2];

		//select file extension
		$read = read_image_type($source);
		if ($read == 'png') {
			$src = imagecreatefrompng($source);
		} elseif ($read == 'jpg') {
			$src = imagecreatefromjpeg($source);
		} elseif ($read == 'gif') {
			$src = imagecreatefromgif($source);
		} else {
			$src = imagecreatefromjpeg($source);
		}

		$dst = imagecreatetruecolor($x[3], $y[3]); //this function save the image
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $x[3], $y[3], $x[1], $y[1]);

		//then save the return with imagejpeg or imagepng and compress if you want
		if ($read == 'jpg') {
			if ($destination != null) {

				imagejpeg($dst, $destination, 100);
			}
			return $dst;
		} elseif ($read == 'png') {
			if ($destination != null) {

				imagepng($dst, $destination);
			}
			return $dst;
		} elseif ($read == 'gif') {
			if ($destination != null) {

				imagegif($dst, $destination);
			}
			return $dst;
		}
	}
}

if (!function_exists('thumbnail_2')) {
	function thumbnail_2($source, $thumbSize, $destination)
	{

		//this was image//Crop and we renamed
		$read = read_image_type($source);
		$sourceImage = null;

		switch ($read) {
			case 'png':
				$sourceImage = imagecreatefrompng($source);
				break;
			case 'jpg':
				$sourceImage = imagecreatefromjpeg($source);
				break;
			case 'gif':
				$sourceImage = imagecreatefromgif($source);
				break;
			case 'webp':
				$sourceImage = imagecreatefromwebp($source);
				break;
			default:
				return false; // Skip thumbnail creation for unsupported formats
		}

		if (!$sourceImage) {
			return false;
		}

		list($srcWidth, $srcHeight) = getimagesize($source);

		if ($srcWidth > $srcHeight) {
			$thumbWidth = $thumbSize;
			$thumbHeight = (int) round(($thumbSize / $srcWidth) * $srcHeight);
		} else {
			$thumbWidth = (int) round(($thumbSize / $srcHeight) * $srcWidth);
			$thumbHeight = $thumbSize;
		}

		$destinationImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
		imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);

		if ($destination !== null) {
			switch ($read) {
				case 'jpg':
					imagejpeg($destinationImage, $destination, 100);
					break;
				case 'png':
					imagepng($destinationImage, $destination);
					break;
				case 'gif':
					imagegif($destinationImage, $destination);
					break;
				case 'webp':
					imagewebp($destinationImage, $destination);
					break;
				default:
					return false;
			}
		}

		imagedestroy($sourceImage);
		imagedestroy($destinationImage);
		return true;
	}
}

if (!function_exists('isPngCompressed')) {
	function isPngCompressed($imagePath)
	{
		// Get the image info
		$imageInfo = getimagesize($imagePath);

		// Check if the image is a PNG
		if ($imageInfo['mime'] == 'image/png') {
			// Open the PNG image
			$image = imagecreatefrompng($imagePath);

			// Check if the PNG image is already compressed
			if (imageistruecolor($image)) {
				// True color PNG, not compressed
				return false;
			} else {
				// Indexed color PNG, already compressed
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('isJpegCompressed')) {
	function isJpegCompressed($imagePath)
	{
		// Get the image info
		$imageInfo = getimagesize($imagePath);

		// Check if the image is a JPEG
		if ($imageInfo['mime'] == 'image/jpeg') {
			// Open the JPEG image
			$image = imagecreatefromjpeg($imagePath);

			// Check if the JPEG image is already compressed
			if (imagecolorstotal($image) == 0) {
				// True color JPEG, not compressed
				return false;
			} else {
				// Indexed color JPEG, already compressed
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('thumbnail')) {
	function thumbnail($source, $destination, $width = 150, $height = true)
	{

		//Notice: only make jpeg thumbnail, for other use thumbnail-v2
		if ($width == false) {

			$width = 150;
		}

		//create gd image from a file
		$image = ImageCreateFromString(file_get_contents($source));

		//calculate resized ratio, note: if $height is set to true then we automatically calculate the height based on the ratio
		$height = $height == true ? (ImageSY($image) * $width / ImageSX($image)) : $height;

		//create image
		$output = ImageCreateTrueColor($width, $height);

		imagecopyresampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

		//save image
		ImageJPEG($output, $destination, 95);

		//return resized image
		return $output; //if you need to use it
	}
}

if (!function_exists('make_facebook_preview')) {
	function make_facebook_preview($input_background, $input_image, $output_image, $small_size = false)
	{

		//input background input image, output is jpeg
		if ($small_size == false) {
			$w = 1200;
			$h = 630;
		} else {
			$w = 600;
			$h = 315;
		}

		//call raw image in a variable from this function function
		$im = image_resize_des($input_background, $w, $h, true);
		$im2 = image_resize_des($input_image, $w, $h);
		imagecopy($im, $im2, (imagesx($im) / 2) - (imagesx($im2) / 2), (imagesy($im) / 2) - (imagesy($im2) / 2), 0, 0, imagesx($im2), imagesy($im2));
		imagejpeg($im, $output_image, 80);
		imagedestroy($im);
		imagedestroy($im2);
		return (array($w, $h));
	}
}

if (!function_exists('readimage')) {
	function readimage($file)
	{
		//show image
		if (file_exists($file)) {

			header('Content-type: ' . 'image/jpg');
			readfile($file);
			exit;
		} elseif (file_exists(urldecode($file))) {

			header('Content-type: ' . 'image/jpg');
			readfile(urldecode($file));
			exit;
		} else {

			http_response_code(404);
			exit;
		}
	}
}

// ///////////////////// HTML FUNCTIONS //////////////////////

if (!function_exists('createNodeFromData')) {
	function createNodeFromData($dom, $data)
	{
		if (!isset($data['nodeType'])) {
			return null;
		}

		switch ($data['nodeType']) {
			case 1:
				$element = $dom->createElement($data['nodeName']);
				if (isset($data['attributes'])) {
					foreach ($data['attributes'] as $attr) {
						$element->setAttribute($attr['name'], $attr['value']);
					}
				}
				if (isset($data['childNodes'])) {
					foreach ($data['childNodes'] as $childData) {
						$childNode = createNodeFromData($dom, $childData);
						if ($childNode) {
							$element->appendChild($childNode);
						}
					}
				}
				return $element;

			case 3:
				if (isset($data['textContent'])) {
					return $dom->createTextNode($data['textContent']);
				}
				return null;

			default:
				return null;
		}
	}
}

if (!function_exists('applyChangesToHtml')) {
	function applyChangesToHtml($htmlString, $jsonChanges)
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);

		$dom->loadHTML('<?xml encoding="UTF-8">' . $htmlString, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
		libxml_clear_errors();

		$xpath = new DOMXPath($dom);
		$changes = is_string($jsonChanges) ? json_decode($jsonChanges, true) : $jsonChanges;

		if (isset($changes['type'])) {
			$changes = [$changes];
		}

		if (!is_array($changes)) {
			return $htmlString;
		}

		foreach ($changes as $change) {
			$type = $change['type'];
			$path = rtrim($change['path'], '/');

			$nodes = $xpath->query($path);
			if ($nodes === false || $nodes->length === 0) {
				continue;
			}

			foreach ($nodes as $node) {
				if (!($node instanceof DOMNode)) {
					throw new InvalidArgumentException("Cannot apply HTML changes to a namespace node at XPath: {$path}");
				}

				switch ($type) {
					case 'attributes_changed':
						if (!($node instanceof DOMElement)) {
							throw new InvalidArgumentException("Attribute changes require an HTML element at XPath: {$path}");
						}

						$attrs = $change['attributes'];
						if (isset($attrs['added'])) {
							foreach ($attrs['added'] as $attr) {
								$node->setAttribute($attr['name'], $attr['value']);
							}
						}
						if (isset($attrs['removed'])) {
							foreach ($attrs['removed'] as $attr) {
								$node->removeAttribute($attr['name']);
							}
						}
						if (isset($attrs['modified'])) {
							foreach ($attrs['modified'] as $attr) {
								$node->setAttribute($attr['name'], $attr['newValue']);
							}
						}
						break;

					case 'text_changed':
						if (isset($change['text'])) {
							$node->nodeValue = $change['text'];
						}
						break;

					case 'added':
						if (isset($change['node'])) {
							$newNode = createNodeFromData($dom, $change['node']);
							if ($newNode) {
								$node->appendChild($newNode);
							}
						}
						break;

					case 'removed':
						if ($node->parentNode) {
							$node->parentNode->removeChild($node);
						}
						break;

					case 'replaced':
						if (isset($change['node'])) {
							$newNode = createNodeFromData($dom, $change['node']);
							if ($newNode && $node->parentNode) {
								$node->parentNode->replaceChild($newNode, $node);
							}
						}
						break;
				}
			}
		}

		$html = $dom->saveHTML();
		$html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$html = str_replace('<?xml encoding="UTF-8">', '', $html);
		return $html;
	}
}

if (!function_exists('push_json_patches')) {
	function push_json_patches($json_data)
	{
		$json_data = str_replace('@id="', '@id="', $json_data);
		$json_data = str_replace('"]', '\"]', $json_data);
		$json_data = str_replace('body[1]/body[1]', 'body[1]', $json_data);
		$json_data = str_replace('html[1]/html[1]', 'html[1]', $json_data);

		return $json_data;
	}
}

if (!function_exists('lowercase_tags')) {
	function lowercase_tags($input)
	{
		$in_tag = false;
		$in_var = false;
		$output = false;
		$length = strlen($input);
		for ($i = 0; $i < $length; $i++) {
			$char = $input[$i];
			if ($in_tag) {
				if ($in_var) {
					if ($char == '"') {
						$in_var = false;
					}
					$output .= $char;
				} else {
					if ($char == '"') {
						$in_var = true;
					} else if ($char == '>') {
						$in_tag = false;
					}
					$output .= strtolower($char);
				}
			} else {
				if ($char == '<') {
					$in_tag = true;
				}
				$output .= $char;
			}
		}

		$output = str_replace('<!doctype html>', '<!DOCTYPE html>', $output);
		return $output;
	}
}

if (!function_exists('html2text')) {
	function html2text($data, $maxwidth = false)
	{
		$check = strip_tags_with_whitespace($data);
		$check = str_replace('&nbsp;', '', $check);
		if ($check == null) {
			return false;
		}

		if ($maxwidth == null) {
			return strip_tags_with_whitespace($data);
		} else {
			$output = strip_tags_with_whitespace($data);
			$output = wordwrap($output, $maxwidth, PHP_EOL, false);
			$output = explode(PHP_EOL, $output);

			$c = count($output);
			if ($c > 1) {
				return $output[0] . '...';
			} else {
				return $output[0];
			}
		}
	}
}

if (!function_exists('strip_comments')) {
	function strip_comments($html)
	{
		$html = str_replace(array("\r\n<!--", "\n<!--"), '<!--', $html);

		while (($pos = strpos($html, '<!--')) !== false) {
			if (($_pos = strpos($html, '-->', $pos)) === false) {
				$html = substr($html, 0, $pos);
			} else {
				$html = substr($html, 0, $pos) . substr($html, $_pos + 3);
			}
		}
		return $html;
	}
}

if (!function_exists('normalize_tags')) {
	function normalize_tags($html)
	{
		// head, caps is not ignored
		$html = str_ireplace('< head', '<head', $html);
		$html = str_ireplace('< / head>', '</head>', $html);
		$html = str_ireplace('</ head>', '</head>', $html);
		$html = str_ireplace('< /head>', '</head>', $html);

		// title
		$html = str_ireplace('< title', '<title', $html);
		$html = str_ireplace('< / title>', '</title>', $html);
		$html = str_ireplace('</ title>', '</title>', $html);
		$html = str_ireplace('< /title>', '</title>', $html);

		// format body
		$html = str_ireplace('< body', '<body', $html);
		$html = str_ireplace('< / body>', '</body>', $html);
		$html = str_ireplace('</ body>', '</body>', $html);
		$html = str_ireplace('< /body>', '</body>', $html);

		// html
		$html = str_ireplace('< html', '<html', $html);
		$html = str_ireplace('< / html>', '</html>', $html);
		$html = str_ireplace('</ html>', '</html>', $html);
		$html = str_ireplace('< /html>', '</html>', $html);

		// images
		$html = str_ireplace('<img', '<img', $html);
		$html = str_ireplace('src = ', 'src=', $html);
		$html = str_ireplace('src =', 'src=', $html);
		$html = str_ireplace('src= ', 'src=', $html);
		$html = str_ireplace('alt = ', 'alt=', $html);
		$html = str_ireplace('alt =', 'alt=', $html);
		$html = str_ireplace('alt= ', 'alt=', $html);
		$html = str_ireplace('class = ', 'class=', $html);
		$html = str_ireplace('class =', 'class=', $html);
		$html = str_ireplace('class= ', 'class=', $html);
		$html = str_ireplace('title = ', 'title=', $html);
		$html = str_ireplace('title =', 'title=', $html);
		$html = str_ireplace('title= ', 'title=', $html);
		$html = str_ireplace('href = ', 'href=', $html);
		$html = str_ireplace('href =', 'href=', $html);
		$html = str_ireplace('href= ', 'href=', $html);
		$html = str_ireplace('target = ', 'target=', $html);
		$html = str_ireplace('target =', 'target=', $html);
		$html = str_ireplace('target= ', 'target=', $html);
		$html = str_ireplace('id = ', 'id=', $html);
		$html = str_ireplace('id =', 'id=', $html);
		$html = str_ireplace('id= ', 'id=', $html);
		$html = str_ireplace('style = ', 'style=', $html);
		$html = str_ireplace('style =', 'style=', $html);
		$html = str_ireplace('style= ', 'style=', $html);
		$html = str_ireplace('onerror = ', 'onerror=', $html);
		$html = str_ireplace('onerror =', 'onerror=', $html);
		$html = str_ireplace('onerror= ', 'onerror=', $html);
		$html = str_ireplace('content = ', 'content=', $html);
		$html = str_ireplace('content =', 'content=', $html);
		$html = str_ireplace('content= ', 'content=', $html);
		return $html;
	}
}

if (!function_exists('links_to_open_in_a_new_tab')) {
	function links_to_open_in_a_new_tab($html)
	{
		$html = str_replace('target="_blank"', '', $html);
		$html = str_replace('target="_self"', '', $html);
		$html = str_replace('target="_parent"', '', $html);
		$html = str_replace('target="_top"', '', $html);
		$html = str_replace('<a ', '<a target="_blank" ', $html);
		return $html;
	}
}

if (!function_exists('limit_html_output')) {
	function limit_html_output($content, $number_of_words_allowed)
	{
		// check if number of words allowed is 0 or less
		if ($number_of_words_allowed <= 0) {
			return $content;
		}

		// check for null html
		if ($content == null) {
			return '';
		}

		// καλύτερα strip_tags αντί για το strip_tags_with_whitespace αλλά να το δούμε και στο μέλλων
		$content = strip_tags($content);
		$content = preg_split('/[\s\xa0]+/', $content);
		$content = array_remove_empty_values($content);

		// count words and stop when reach the max number
		$j = 0;
		foreach ($content as $per_word) {
			$j++;
			$contents_preview[] = $per_word;
			if ($j == $number_of_words_allowed) {
				break;
			}
		}
		$contents_preview = implode(' ', $contents_preview);

		// format again as html so can replace html code
		$contents_preview = '<p>' . $contents_preview . '...</p>';
		return $contents_preview;
	}
}

if (!function_exists('update_canonical_2')) {
	function update_canonical_2($html, $canonical_url = false)
	{
		if ($html == null) {
			return $html;
		}

		if ($canonical_url == false) {
			$canonical_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}

		$canonical_url = htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8');

		// More robust pattern that preserves any existing attributes
		$pattern = '/(<link[^>]*rel=["\']canonical["\'][^>]*href=["\'])[^"\']*(["\'][^>]*>)/i';
		$replacement = '${1}' . $canonical_url . '${2}';

		// Handle alternate order (href before rel)
		$pattern2 = '/(<link[^>]*href=["\'])[^"\']*(["\'][^>]*rel=["\']canonical["\'][^>]*>)/i';
		$replacement2 = '${1}' . $canonical_url . '${2}';

		$count = 0;
		$html = preg_replace($pattern, $replacement, $html, -1, $count);
		$html = preg_replace($pattern2, $replacement2, $html, -1, $count2);

		// If canonical not found, append one before </head>
		if (($count + $count2) === 0) {
			$canonical_tag = '<link rel="canonical" href="' . $canonical_url . '">';
			if (stripos($html, '</head>') !== false) {
				$html = preg_replace('/<\/head>/i', "\n\t" . $canonical_tag . "\n</head>", $html, 1);
			}
		}

		return $html;
	}
}

if (!function_exists('normalize_meta_description')) {
	function normalize_meta_description($html)
	{
		// other
		$html = str_ireplace('<meta name="description"  content', '<meta name="description" content', $html);

		// format meta code
		$html = str_ireplace('<meta name="description" content="', '<meta name="description" content="', $html);
		$html = str_ireplace('<meta  name="description"  content="', '<meta name="description" content="', $html);
		$html = str_ireplace('<meta  name="description" content="', '<meta name="description" content="', $html);
		$html = str_ireplace('<meta name="description"  content="', '<meta name="description" content="', $html);
		$html = str_ireplace('<meta name = "description"  content = "', '<meta name="description" content="', $html);
		$html = str_ireplace('<meta name= "description"  content= "', '<meta name="description" content="', $html);
		$html = str_ireplace('<meta name ="description"  content ="', '<meta name="description" content="', $html);

		$html = str_ireplace("<meta  name='description'  content='", "<meta name='description' content='", $html);
		$html = str_ireplace("<meta  name='description' content='", "<meta name='description' content='", $html);
		$html = str_ireplace("<meta name='description'  content='", "<meta name='description' content='", $html);
		$html = str_ireplace("<meta name = 'description'  content = '", "<meta name='description' content='", $html);
		$html = str_ireplace("<meta name= 'description'  content= '", "<meta name='description' content='", $html);
		$html = str_ireplace("<meta name ='description'  content ='", "<meta name='description' content='", $html);
		return $html;
	}
}

if (!function_exists('normalize_meta_keywords')) {
	function normalize_meta_keywords($html)
	{
		// other
		$html = str_ireplace('<meta name="keywords"  content', '<meta name="keywords" content', $html);

		// format meta code
		$html = str_ireplace('<meta name="keywords" content="', '<meta name="keywords" content="', $html);
		$html = str_ireplace('<meta  name="keywords"  content="', '<meta name="keywords" content="', $html);
		$html = str_ireplace('<meta  name="keywords" content="', '<meta name="keywords" content="', $html);
		$html = str_ireplace('<meta name="keywords"  content="', '<meta name="keywords" content="', $html);
		$html = str_ireplace('<meta name = "keywords"  content = "', '<meta name="keywords" content="', $html);
		$html = str_ireplace('<meta name= "keywords"  content= "', '<meta name="keywords" content="', $html);
		$html = str_ireplace('<meta name ="keywords"  content ="', '<meta name="keywords" content="', $html);

		$html = str_ireplace("<meta  name='keywords'  content='", "<meta name='keywords' content='", $html);
		$html = str_ireplace("<meta  name='keywords' content='", "<meta name='keywords' content='", $html);
		$html = str_ireplace("<meta name='keywords'  content='", "<meta name='keywords' content='", $html);
		$html = str_ireplace("<meta name = 'keywords'  content = '", "<meta name='keywords' content='", $html);
		$html = str_ireplace("<meta name= 'keywords'  content= '", "<meta name='keywords' content='", $html);
		$html = str_ireplace("<meta name ='keywords'  content ='", "<meta name='keywords' content='", $html);
		return $html;
	}
}

if (!function_exists('export_meta_description')) {
	function export_meta_description($html)
	{
		// get meta, and try many formats
		// todo: supports only double marks only, should support the one mark
		$html = normalize_meta_description($html);
		$meta = str_between($html, '<meta name="description" content="', '">');
		$meta = '<meta name="description" content="' . $meta . '">';

		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '"/>');
			$meta = '<meta name="description" content="' . $meta . '"/>';
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '" />');
			$meta = '<meta name="description" content="' . $meta . '" />';
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '"/ >');
			$meta = '<meta name="description" content="' . $meta . '"/ >';
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '>');
			$meta = '<meta name="description" content="' . $meta . '>';
		}

		return $meta;
	}
}

if (!function_exists('read_meta_description')) {
	function read_meta_description($html)
	{
		// get meta, and try many formats
		// todo: supports only double marks only, should support the one mark
		// Normalize whitespace (handles multiline meta tags)
		$html = preg_replace('/\s+/', ' ', $html);
		$html = normalize_meta_description($html);
		$meta = str_between($html, '<meta name="description" content="', '">');
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '"/>');
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '" />');
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '"/ >');
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="description" content="', '>');
		}

		return $meta;
	}
}

if (!function_exists('read_meta_keywords')) {
	function read_meta_keywords($html)
	{
		// get meta, and try many formats
		// todo: supports only double marks only, should support the one mark
		// Normalize whitespace (handles multiline meta tags)
		$html = preg_replace('/\s+/', ' ', $html);
		$html = normalize_meta_keywords($html);
		$meta = str_between($html, '<meta name="keywords" content="', '">');
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '"/>');
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '" />');
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '"/ >');
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '>');
		}

		return $meta;
	}
}

if (!function_exists('export_meta_keywords')) {
	function export_meta_keywords($html)
	{
		// get meta, and try many formats
		// todo: supports only double marks only, should support the one mark
		$html = normalize_meta_keywords($html);
		$meta = str_between($html, '<meta name="keywords" content="', '">');
		$meta = '<meta name="keywords" content="' . $meta . '">';

		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '"/>');
			$meta = '<meta name="keywords" content="' . $meta . '"/>';
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '" />');
			$meta = '<meta name="keywords" content="' . $meta . '" />';
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '"/ >');
			$meta = '<meta name="keywords" content="' . $meta . '"/ >';
		}
		if (strpos($meta, "\n") !== false || strpos($meta, "\r") !== false || strpos(convert2flat($meta), '><') !== false || $meta == null) {
			$meta = str_between($html, '<meta name="keywords" content="', '>');
			$meta = '<meta name="keywords" content="' . $meta . '>';
		}

		return $meta;
	}
}

if (!function_exists('update_meta_description')) {
	function update_meta_description($html, $new_meta_data)
	{
		// εξετάζει αν ειναι κενό το δεδομένο
		if (trim($new_meta_data) == null) {
			return $html;
		}

		$meta = export_meta_description($html);
		// todo: supports only double marks only, should support the one mark
		$new_meta = '<meta name="description" content="' . $new_meta_data . '">';

		// fix meta tag url
		$html = normalize_meta_description($html);
		$html = str_ireplace($meta, $new_meta, $html);
		return $html;
	}
}

if (!function_exists('update_meta_keywords')) {
	function update_meta_keywords($html, $new_meta_data)
	{
		// εξετάζει αν ειναι κενό το δεδομένο
		if (trim($new_meta_data) == null) {
			return $html;
		}

		$meta = export_meta_keywords($html);
		// todo: supports only double marks only, should support the one mark
		$new_meta = '<meta name="keywords" content="' . $new_meta_data . '">';

		// fix meta tag url
		$html = normalize_meta_keywords($html);
		$html = str_ireplace($meta, $new_meta, $html);
		return $html;
	}
}

if (!function_exists('read_title')) {
	function read_title($html)
	{
		$html = normalize_tags($html);
		$html = lowercase_tags($html);

		// εν τέλει αποφάσισα ότι μέσα απο τα tags είναι ο ποιο έγκυρος τίτλος
		$title = str_between($html, '<title>', '</title>');

		return $title;
	}
}

if (!function_exists('read_menu_title')) {
	function read_menu_title($html)
	{
		$pattern = '/<meta\s+name="menu-title"\s+content="([^"]+)"/i';
		if (preg_match($pattern, $html, $matches)) {
			return $matches[1];
		}
		return null;
	}
}

if (!function_exists('read_date_format')) {
	function read_date_format($html)
	{
		$pattern = '/<meta\s+name="date-format"\s+content="([^"]+)"/i';
		if (preg_match($pattern, $html, $matches)) {
			return $matches[1];
		}
		return 'd-m-y';
	}
}

if (!function_exists('read_main_html')) {
	function read_main_html($html)
	{
		if ($html == null) {
			return null;
		}

		$innerHTML = read_elem_by_class($html, 'html');
		if ($innerHTML !== null) {
			return $innerHTML;
		}

		$pattern = '/<([a-z][a-z0-9]*)\b(?=[^>]*\bid\s*=\s*["\']html["\'])[^>]*>(.*?)(?:<\/\1>|$)/isu';
		if (preg_match($pattern, $html, $matches)) {
			return html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		return null;
	}
}

if (!function_exists('read_main_image')) {
	function read_main_image($html)
	{
		$src = read_attribute_by_class($html, 'image', 'src');
		return $src !== null ? trim($src) : null;
	}
}

if (!function_exists('has_img_tag_with_class_image')) {
	function has_img_tag_with_class_image($html)
	{
		if ($html == null) {
			return false;
		}

		return preg_match('/<img\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?image(?:[[:space:]][^"\']*)?["\'])[^>]*>/isu', $html) === 1;
	}
}

if (!function_exists('has_video_tag_with_class_video')) {
	function has_video_tag_with_class_video($html)
	{
		if ($html == null) {
			return false;
		}

		return preg_match('/<video\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?video(?:[[:space:]][^"\']*)?["\'])[^>]*>/isu', $html) === 1;
	}
}

if (!function_exists("read_main_video")) {

	function read_main_video($html)
	{
		if ($html == null) {
			return null;
		}

		$classEscaped = preg_quote('video', '/');
		$pattern = '/(<video\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>)(.*?)(?:<\/video>|$)/isu';

		if (preg_match($pattern, $html, $matches)) {
			if (preg_match('/\bsrc\s*=\s*["\']([^"\']*)["\']/isu', $matches[1], $srcMatches)) {
				$src = trim(html_entity_decode($srcMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
				if ($src !== '') {
					return $src;
				}
			}

			if (preg_match('/<source\b[^>]*\bsrc\s*=\s*["\']([^"\']*)["\'][^>]*>/isu', $matches[2], $sourceMatches)) {
				$sourceSrc = trim(html_entity_decode($sourceMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
				return $sourceSrc !== '' ? $sourceSrc : null;
			}
		}

		return null;
	}
}

if (!function_exists('update_main_video')) {
	// Hard update video src for all <video> tags (and nested <source> tags)
	function update_main_video($html, $video_src)
	{
		if ($html == null || $video_src == null || $video_src === '') {
			return $html;
		}

		$path = parse_url($video_src, PHP_URL_PATH) ?: '';
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		$type = 'video/mp4';
		if ($ext === 'webm') {
			$type = 'video/webm';
		} elseif ($ext === 'ogg' || $ext === 'ogv') {
			$type = 'video/ogg';
		}

		$video_src_escaped = htmlspecialchars($video_src, ENT_QUOTES, 'UTF-8');
		$type_escaped = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');

		$classEscaped = preg_quote('video', '/');
		$pattern = '/(<video\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>)(.*?)(<\/video>)/isu';

		$html = preg_replace_callback($pattern, function ($matches) use ($video_src_escaped, $type_escaped) {
			$openingTag = $matches[1];
			$inner = $matches[2];

			// Update or append src in <video>
			if (preg_match('/\bsrc\s*=\s*["\'][^"\']*["\']/i', $openingTag)) {
				$openingTag = preg_replace('/\bsrc\s*=\s*["\'][^"\']*["\']/i', 'src="' . $video_src_escaped . '"', $openingTag, 1);
			} else {
				$openingTag = preg_replace('/>$/', ' src="' . $video_src_escaped . '">', $openingTag, 1);
			}

			// Update all existing <source> tags
			$sourceCount = 0;
			$inner = preg_replace_callback('/<source\b[^>]*>/i', function ($sourceMatch) use ($video_src_escaped, $type_escaped) {
				$sourceTag = $sourceMatch[0];
				if (preg_match('/\bsrc\s*=\s*["\'][^"\']*["\']/i', $sourceTag)) {
					$sourceTag = preg_replace('/\bsrc\s*=\s*["\'][^"\']*["\']/i', 'src="' . $video_src_escaped . '"', $sourceTag, 1);
				} else {
					$sourceTag = preg_replace('/\/>$/', ' src="' . $video_src_escaped . '"/>', $sourceTag, 1);
					$sourceTag = preg_replace('/>$/', ' src="' . $video_src_escaped . '">', $sourceTag, 1);
				}

				if (preg_match('/\btype\s*=\s*["\'][^"\']*["\']/i', $sourceTag)) {
					$sourceTag = preg_replace('/\btype\s*=\s*["\'][^"\']*["\']/i', 'type="' . $type_escaped . '"', $sourceTag, 1);
				} else {
					$sourceTag = preg_replace('/\/>$/', ' type="' . $type_escaped . '"/>', $sourceTag, 1);
					$sourceTag = preg_replace('/>$/', ' type="' . $type_escaped . '">', $sourceTag, 1);
				}

				return $sourceTag;
			}, $inner, -1, $sourceCount);

			// If no <source> exists, append one
			if ($sourceCount === 0) {
				$inner .= '<source src="' . $video_src_escaped . '" type="' . $type_escaped . '">';
			}

			return $openingTag . $inner . $matches[3];
		}, $html);

		return $html;
	}
}

if (!function_exists('read_description_limited')) {
	function read_description_limited($html)
	{
		// Get the meta description content
		$description = read_meta_description($html);
		if ($description == '') {
			$description = read_main_html($html);
			// Strip HTML tags
			$description = strip_tags($description);
			// Convert HTML entities to characters
			$description = html_entity_decode($description);
			// Trim to meta description length (typically 155-160 chars)
			$description = mb_substr($description, 0, 155);
			// Add ellipsis if truncated
			if (mb_strlen($description) == 155) {
				$description .= '...';
			}
		}
		return $description;
	}
}

if (!function_exists('update_menu_title')) {
	function update_menu_title($html, $menu_title)
	{
		// Deprecation with call user data in db json

		// Pattern to match the existing menu-title meta tag
		$pattern = '/<meta\s+name="menu-title"\s+content="[^"]*"/i';

		// New meta tag with updated content
		$replacement = '<meta name="menu-title" content="' . htmlspecialchars($menu_title, ENT_QUOTES, 'UTF-8') . '"';

		// If meta tag exists, replace it
		if (preg_match($pattern, $html)) {
			$html = preg_replace($pattern, $replacement, $html);
		} else {
			// If meta tag doesn't exist, insert it after opening head tag
			$html = preg_replace('/(<head[^>]*>)/i', '$1' . "\n\t" . $replacement . '>', $html);
		}

		return $html;
	}
}

if (!function_exists('update_date_format')) {
	function update_date_format($html, $date_format)
	{
		$pattern = '/<meta\s+name="date-format"\s+content="[^"]*"/i';
		$replacement = '<meta name="date-format" content="' . htmlspecialchars($date_format, ENT_QUOTES, 'UTF-8') . '"';

		if (preg_match($pattern, $html)) {
			$html = preg_replace($pattern, $replacement, $html);
		} else {
			$html = preg_replace('/(<head[^>]*>)/i', '$1' . "\n\t" . $replacement . '>', $html);
		}

		return $html;
	}
}

if (!function_exists('update_main_html')) {
	function update_main_html($html, $new_content)
	{
		// If content is null dont update
		if (empty($new_content)) {
			return $html;
		}

		// Keep behavior aligned with class-pattern updates (no DOM loadHTML)
		return update_elem_by_class($html, 'html', $new_content);
	}
}

if (!function_exists('update_main_image')) {
	function update_main_image($html, $image_src, $title)
	{
		// If image_src is null dont update
		if ($html == null || $image_src == null) {
			return $html;
		}

		$image_src_escaped = htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8');
		$title_escaped = htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8');

		$classEscaped = preg_quote('image', '/');
		$pattern = '/<img\b((?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*)(\/?)>/isu';

		$html = preg_replace_callback($pattern, function ($matches) use ($image_src_escaped, $title_escaped) {
			$attrs = $matches[1];
			$selfClose = $matches[2];

			// Remove old values (if present)
			$attrs = preg_replace('/\s+src\s*=\s*["\'][^"\']*["\']/i', '', $attrs);
			$attrs = preg_replace('/\s+title\s*=\s*["\'][^"\']*["\']/i', '', $attrs);
			$attrs = preg_replace('/\s+alt\s*=\s*["\'][^"\']*["\']/i', '', $attrs);

			return '<img' . $attrs . ' src="' . $image_src_escaped . '" title="' . $title_escaped . '" alt="' . $title_escaped . '"' . $selfClose . '>';
		}, $html);

		return $html;
	}
}

if (!function_exists('update_og_meta_tags')) {
	function update_og_meta_tags($html, $og_tags)
	{
		// If no tags to update, return original HTML unchanged
		if (empty($og_tags) || $html == null) {
			return $html;
		}

		foreach ($og_tags as $property => $content) {
			// Format property name
			$property = trim((string) $property);
			if ($property === '') {
				continue;
			}

			// Escape values for regex / html
			$propertyEscaped = preg_quote($property, '/');
			$contentEscaped = htmlspecialchars((string) $content, ENT_QUOTES, 'UTF-8');

			// More robust patterns that preserve existing attributes and attribute order
			$pattern = '/(<meta\b[^>]*\bproperty\s*=\s*["\']' . $propertyEscaped . '["\'][^>]*\bcontent\s*=\s*["\'])[^"\']*(["\'][^>]*>)/isu';
			$replacement = '${1}' . $contentEscaped . '${2}';

			// Handle alternate order (content before property)
			$pattern2 = '/(<meta\b[^>]*\bcontent\s*=\s*["\'])[^"\']*(["\'][^>]*\bproperty\s*=\s*["\']' . $propertyEscaped . '["\'][^>]*>)/isu';
			$replacement2 = '${1}' . $contentEscaped . '${2}';

			$count = 0;
			$count2 = 0;
			$html = preg_replace($pattern, $replacement, $html, -1, $count);
			$html = preg_replace($pattern2, $replacement2, $html, -1, $count2);

			// If tag not found, append it inside <head>
			if (($count + $count2) === 0) {
				$metaTag = '<meta property="' . htmlspecialchars($property, ENT_QUOTES, 'UTF-8') . '" content="' . $contentEscaped . '">';

				if (stripos($html, '</head>') !== false) {
					$html = preg_replace('/<\/head>/i', "\n\t" . $metaTag . "\n</head>", $html, 1);
				} elseif (preg_match('/<head\b[^>]*>/i', $html)) {
					$html = preg_replace('/(<head\b[^>]*>)/i', '$1' . "\n\t" . $metaTag, $html, 1);
				}
			}
		}

		return $html;
	}
}

if (!function_exists('update_title')) {
	function update_title($html, $title)
	{
		// εξετάζει αν ειναι κενό το δεδομένο
		if ($title == null or trim($title) == null) {
			return $html;
		}

		// build correct title
		$title = strip_tags($title);
		$title = preg_replace("/\r\n|\r|\n/", ' ', $title);
		$title = preg_replace('!\s+!', ' ', $title);

		// update title tag
		$html = normalize_tags($html);
		$title_old = str_between($html, '<title>', '</title>');
		$title_old = '<title>' . $title_old . '</title>';
		$title = '<title>' . $title . '</title>';
		$html = str_replace($title_old, $title, $html);
		return $html;
	}
}

if (!function_exists('update_h1')) {
	function update_h1($html, $new_content)
	{
		if (trim($new_content) == null) {
			return $html;
		}
		$new_h1_content = htmlspecialchars($new_content, ENT_QUOTES, 'UTF-8');

		// Use callback to replace while preserving the h1 tag with all its attributes
		$html = preg_replace_callback(
			'/(<h1\b[^>]*>)(.*?)(<\/h1>)/is',
			function ($matches) use ($new_h1_content) {
				return $matches[1] . $new_h1_content . $matches[3];
			},
			$html
		);

		// If no h1 tag was found, insert it at the beginning.
		if (stripos($html, '<h1') === false) {
			$html = '<h1>' . $new_h1_content . '</h1>' . $html;
		}

		return $html;
	}
}

if (!function_exists('update_elem_by_class')) {
	function update_elem_by_class($html, $className, $newContent)
	{
		if ($html == null) {
			return $html;
		} elseif ($newContent == null) {
			return $html;
		}

		// Format class name
		$className = mb_strtolower(str_replace(' ', '-', $className));

		// Escape class name for use in regex
		$classEscaped = preg_quote($className, '/');

		// Match opening tag (any tag) that contains className as a full class token,
		// not as a substring of another class (e.g. "url" should not match "climate-url").
		$pattern = '/(<([a-z][a-z0-9]*)\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>)(.*?)(<\/\2>)/isu';

		$html = preg_replace_callback($pattern, function ($matches) use ($newContent) {
			// $matches[1] = opening tag, $matches[3] = old inner content, $matches[4] = closing tag
			return $matches[1] . $newContent . $matches[4];
		}, $html);

		return $html;
	}
}

if (!function_exists('delete_elem_by_class')) {
	function delete_elem_by_class($html, $className)
	{
		if ($html == null) {
			return $html;
		}

		// Format class name
		$className = mb_strtolower(str_replace(' ', '-', $className));

		// Escape class name for use in regex
		$classEscaped = preg_quote($className, '/');

		// Match the entire element only when className exists as a full class token.
		// Uses backreference \1 to match the correct closing tag.
		$pattern = '/<([a-z][a-z0-9]*)\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>.*?<\/\1>/isu';

		$html = preg_replace($pattern, '', $html);

		return $html;
	}
}

if (!function_exists('read_elem_by_class')) {
	function read_elem_by_class($html, $className)
	{
		if ($html == null) {
			return $html;
		}

		// Format class name
		$className = mb_strtolower(str_replace(' ', '-', $className));

		// Escape class name for use in regex
		$classEscaped = preg_quote($className, '/');

		// Match opening tag (any tag) that contains the class, its inner content, and matching closing tag
		$pattern = '/(<([a-z][a-z0-9]*)\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>)(.*?)(?:<\/\2>|$)/isu';

		if (preg_match($pattern, $html, $matches)) {
			return html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		return null;
	}
}

if (!function_exists('exists_elem_by_class')) {
	function exists_elem_by_class($html, $className)
	{
		if ($html === null) {
			return false;
		}

		// Format class name
		$className = mb_strtolower(str_replace(' ', '-', $className));

		// Escape class name for use in regex
		$classEscaped = preg_quote($className, '/');

		return preg_match('/<([a-z][a-z0-9]*)\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>/isu', $html) === 1;
	}
}

if (!function_exists('update_attribute_by_class')) {
	function update_attribute_by_class($html, $className, $attributeName, $newValue)
	{
		if ($html == null) {
			return $html;
		} elseif ($newValue == null) {
			return $html;
		}

		// Format class name and attribute name
		$className = mb_strtolower(str_replace(' ', '-', $className));
		$attributeName = mb_strtolower(str_replace(' ', '-', $attributeName));

		// Escape values for regex / html
		$classEscaped = preg_quote($className, '/');
		$attributeEscaped = preg_quote($attributeName, '/');
		$newValueEscaped = htmlspecialchars((string) $newValue, ENT_QUOTES, 'UTF-8');

		// Match the complete opening tag that contains the class.
		$pattern = '/<([a-z][a-z0-9]*)\b((?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*)(\s*\/?)>/isu';

		$html = preg_replace_callback($pattern, function ($matches) use ($attributeName, $attributeEscaped, $newValueEscaped) {
			$tagName = $matches[1];
			$attributes = $matches[2];
			$selfClose = $matches[3];
			$tagEnd = '>';

			if (!preg_match('/\s+' . $attributeEscaped . '\s*=/i', $attributes)) {
				return '<' . $tagName . $attributes . $selfClose . $tagEnd;
			}

			// Remove all existing copies of this attribute so malformed tags like href="a" href="b" become clean.
			$attributes = preg_replace('/\s+' . $attributeEscaped . '\s*=\s*(["\'])[^"\']*\1/i', '', $attributes);
			$attributes = preg_replace('/\s+' . $attributeEscaped . '\s*=\s*[^\s>]+/i', '', $attributes);

			return '<' . $tagName . $attributes . ' ' . $attributeName . '="' . $newValueEscaped . '"' . $selfClose . $tagEnd;
		}, $html);

		return $html;
	}
}

if (!function_exists('read_attribute_by_class')) {
	function read_attribute_by_class($html, $className, $attributeName)
	{
		if ($html == null) {
			return $html;
		}

		// Format class name and attribute name
		$className = mb_strtolower(str_replace(' ', '-', $className));
		$attributeName = mb_strtolower(str_replace(' ', '-', $attributeName));

		// Escape values for regex
		$classEscaped = preg_quote($className, '/');
		$attributeEscaped = preg_quote($attributeName, '/');

		$pattern = '/<([a-z][a-z0-9]*)\b(?=[^>]*\bclass\s*=\s*["\'](?:[^"\']*[[:space:]])?' . $classEscaped . '(?:[[:space:]][^"\']*)?["\'])[^>]*>/isu';

		if (preg_match($pattern, $html, $matches)) {
			if (preg_match('/\b' . $attributeEscaped . '\s*=\s*["\']([^"\']*)["\']/isu', $matches[0], $attributeMatches)) {
				return html_entity_decode($attributeMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
			}

			if (preg_match('/\b' . $attributeEscaped . '\s*=\s*([^\s>]+)/isu', $matches[0], $attributeMatches)) {
				return html_entity_decode($attributeMatches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
			}
		}

		return null;
	}
}

if (!function_exists('get_inner_html')) {
	function get_inner_html($node)
	{
		$innerHTML = '';
		foreach ($node->childNodes as $child) {
			$innerHTML .= $node->ownerDocument->saveHTML($child);
		}
		return $innerHTML;
	}
}

if (!function_exists('remove_spaces_between_tags')) {
	function remove_spaces_between_tags($html)
	{
		if (!$html)
			return $html;

		// Remove line endings and whitespace between tags
		$html = preg_replace('/>\s*[\r\n\t]*\s*</', '><', $html);

		// Remove whitespace and line endings at start/end
		$html = trim($html);

		// Preserve single space inside tags
		$html = preg_replace('/\s+/', ' ', $html);

		// Remove spaces/line endings before closing tags
		$html = preg_replace('/\s+>/', '>', $html);

		// Remove spaces/line endings after opening tags
		$html = preg_replace('/<\s+/', '<', $html);

		return $html;
	}
}

if (!function_exists('encode_email_for_show_in_html')) {
	function encode_email_for_show_in_html($email)
	{
		$email = str_replace('#', '@', $email);
		$email = str_replace('*', '.', $email);
		$email = str_replace('~', '-', $email);
		return $email;
	}
}

if (!function_exists('decode_email_for_use_in_html')) {
	function decode_email_for_use_in_html($email)
	{
		$email = str_replace('@', '#', $email);
		$email = str_replace('.', '*', $email);
		$email = str_replace('-', '~', $email);
		return $email;
	}
}

if (!function_exists('sanitizeHtmlContent')) {
	function sanitizeHtmlContent($content)
	{

		//Note: This was fixing incomplete html and we dont this use any more on chat

		if ($content == null) {
			return trim((string) $content);
		}

		// Remove script tags and their content
		$sanitizedContent = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $content);

		// Remove on* attributes (e.g., onclick, onload, etc.)
		$sanitizedContent = preg_replace('/\s+on[a-z0-9_:-]+\s*=\s*"[^"]*"/i', '', $sanitizedContent);
		$sanitizedContent = preg_replace('/\s+on[a-z0-9_:-]+\s*=\s*\'[^\']*\'/i', '', $sanitizedContent);
		$sanitizedContent = preg_replace('/\s+on[a-z0-9_:-]+\s*=\s*[^\s>]+/i', '', $sanitizedContent);

		// Decode numeric entities back to characters
		$sanitizedContent = html_entity_decode($sanitizedContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');

		// Trim and return the sanitized content
		return trim($sanitizedContent);
	}
}
//////////////////////////////// FILESYSTEM FUNCTIONS ////////////////////////////////

if (!function_exists('create_path')) {
	function create_path($path)
	{
		if (str_ends_with($path, '/')) {
			$path = rtrim($path, '/');
		}
		if (is_dir($path)) {
			return true;
		}

		// $pos = strrpos($path, '/', -2) + 1;
		// $prev_path = substr($path, 0, $pos);
		// $return = create_path($prev_path);
		// return ($return && is_writable($prev_path)) ? mkdir($path) : false;

		// new
		if (is_dir($path)) {
			return true;
		} elseif (mkdir($path, 0777, true)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('create_file')) {
	function create_file($file_with_full_path)
	{
		$dirname = dirname($file_with_full_path);
		create_path($dirname);
		touch($file_with_full_path);
	}
}

if (!function_exists('normalize_path')) {
	function normalize_path($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('|(?<=.)/+|', '/', $path);
		if (':' === substr($path, 1, 1)) {
			$path = ucfirst($path);
		}
		return $path;
	}
}

if (!function_exists('delete_directory')) {
	function delete_directory($dirPath)
	{
		if (is_dir($dirPath)) {
			$objects = scandir($dirPath);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == 'dir') {
						delete_directory($dirPath . DIRECTORY_SEPARATOR . $object);
					} else {
						unlink($dirPath . DIRECTORY_SEPARATOR . $object);
					}
				}
			}

			reset($objects);
			rmdir($dirPath);
		}
	}
}

if (!function_exists('copy_directory')) {
	function copy_directory($src, $dst)
	{
		$dir = opendir($src);
		@mkdir($dst);
		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					copy_directory($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
}

if (!function_exists('modify_extension')) {
	function modify_extension($file, $ext_new)
	{
		$ext = '.' . get_extension($file);
		$ext_new = '.' . $ext_new;
		$file = str_replace($ext, $ext_new, $file);
		return $file;
	}
}

if (!function_exists('get_mine_type')) {
	function get_mine_type($filename)
	{
		$idx = explode('.', $filename);
		$count_explode = count($idx);
		$idx = strtolower($idx[$count_explode - 1]);

		$mimet = array(
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',
			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',
			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',
			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			'docx' => 'application/msword',
			'xlsx' => 'application/vnd.ms-excel',
			'pptx' => 'application/vnd.ms-powerpoint',
			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		if (isset($mimet[$idx])) {
			return $mimet[$idx];
		} else {
			return 'application/octet-stream';
		}
	}
}

if (!function_exists('get_extension')) {
	function get_extension($filename)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$ext = strtolower($ext);
		return $ext;
	}
}

if (!function_exists('remove_extension')) {
	function remove_extension($filename)
	{
		$ext = get_extension($filename);
		return str_replace('.' . $ext, '', $filename);
	}
}

if (!function_exists('is_old_version')) {
	function is_old_version($file)
	{
		// check if file has a date in 6 digits format, photius format
		if (preg_match('~(?<!\d)\d{6}(?!\d)~', $file)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('rtrim_with_replace')) {
	function rtrim_with_replace($string, $trim_chars, $replace_with)
	{
		$trimmed_string = rtrim($string, $trim_chars);
		if ($trimmed_string !== $string) {
			return $trimmed_string . $replace_with;
		}
		return $string;
	}
}

if (!function_exists('url2path')) {
	function url2path($url, $root_path = false, $root_url = false)
	{
		// να κάνει encode αν έχει %.. αλιώς δεν λειτουργεί με τα % μέσα στο server!!!
		$url = rawurldecode($url);  // ας μην κάνει encode τελικά, θέλουμε να γράφει % στο σύστημα αρχείων αντί για ότι νανε χαρακτήρες

		// trim the request - αποφάσησα ότι δεν έχει νοημα γιατί διμουργει ποιο πολλά προβλήματα από όσα λύνει, δεν υπάρχει και τέτοιο αρχέιο γενικά
		if (strpos($url, '?') !== false) {
			$url = explode('?', $url);
			$url = $url[0];
		}

		// εδώ επιλέγει αν θα βάλει τα path και url που θα του στείλουμε ή αν όχι
		// note: μπήκε ή νέα μέθοδος που ψάχνει και έξω απο το domain το root
		if ($root_path == true && $root_url == true) {
			// make path from url
			$path = str_ireplace($root_url, $root_path, $url);
		} else {
			$root_url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://';
			$root_url = isset($_SERVER['HTTP_HOST']) ? $root_url . $_SERVER['HTTP_HOST'] : '';

			$root_path = $_SERVER['DOCUMENT_ROOT'];

			// transform url 2 file cross http or https for secure only or not
			if (is_secure()) {
				$url = str_replace('http://', 'https://', $url);
			} else {
				$url = str_replace('https://', 'http://', $url);
			}

			// if url have an other host then get host and replace it with server host
			if (is_cli() && isset($_SERVER['HTTP_HOST'])) {

				//console only patch
				$parsed_url = parse_url($url);
				if (isset($parsed_url['host']) && $parsed_url['host'] !== $_SERVER['HTTP_HOST']) {
					$host = $_SERVER['HTTP_HOST'];
					// Remove port if present in the original URL
					$host = preg_replace('/:\d+$/', '', $host);
					$url = str_replace($parsed_url['host'], $host, $url);
				}
				// Remove port if present in the URL
				$url = preg_replace('/:\d+/', '', $url);
			}

			// make cross www url
			$root_url = str_replace('https://www.', 'https://', $root_url);
			$root_url = str_replace('http://www.', 'http://', $root_url);
			$url = str_replace('https://www.', 'https://', $url);
			$url = str_replace('http://www.', 'http://', $url);

			// make path from url
			$path = str_ireplace($root_url, $root_path, $url);
		}

		// new επειδή το url πάντα δείχνει αρχείο, θα γίνει το ίδιο και όταν είναι για path
		// η διαφορά όμως σε σχέση με το url είναι το path πρεπει πάντα να έχει index αλιώς είναι φάκελος
		$path = patch_url_with_index($path);

		// if path contains https then return false
		if (strpos($path, 'https://') !== false) {
			return false;
		}
		return $path;
	}
}

if (!function_exists('file_get_contents_or_null')) {
	function file_get_contents_or_null($filename)
	{
		if (file_exists($filename)) {
			return file_get_contents($filename);
		} else {
			return '';  // return this in place of null because php 8 show allerts with null
		}
	}
}

/**
 * Gets the data from a URL with CURL
 *
 * @param string $url The URL to be requested
 * @param array $fields An array of fields to be posted
 * @return string The contents of the URL or false if the request fails
 */
if (!function_exists('file_get_contents_curl')) {
	function file_get_contents_curl($url, $fields = array())
	{
		/* gets the data from a URL with CURL */
		$fields_string = null;

		// open connection
		if (function_exists('curl_init')) {
			$ch = curl_init();

			// set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL, $url);

			if (count($fields) > 0) {
				// url-ify the data for the POST
				foreach ($fields as $key => $value) {
					$fields_string .= $key . '=' . $value . '&';
				}
				rtrim($fields_string, '&');

				curl_setopt($ch, CURLOPT_POST, count($fields));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);  // set this 4 sec don't spend one life here

			// set a user again to pass the error 403 Forbidden note: always at the last option
			// $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
			$user_agent = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3B48b Safari/419.3';  // please use mobile to emulate google crawler
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			$data = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($httpCode >= 200 && $httpCode < 300) {
				curl_close($ch);
				return $data;
			} else {
				curl_close($ch);
				return false;
			}
		} else {
			trigger_error('curl not found');
			return false;
		}
	}
}

if (!function_exists('file_get_contents_or_download')) {
	function file_get_contents_or_download($file)
	{
		static $cache = array();

		if (array_key_exists($file, $cache)) {
			return $cache[$file];
		}

		$url = path2url($file);
		if (array_key_exists($url, $cache)) {
			$cache[$file] = $cache[$url];
			return $cache[$url];
		}

		if (file_exists($file) && !is_dir($file)) {
			$html = file_get_contents($file);
			$cache[$file] = $html;
			$cache[$url] = $html;
			return $html;
		} else {
			$html = file_get_contents_curl($url);
			if ($html == true) {

				// New only download and dont write html on fs
				$cache[$file] = $html;
				$cache[$url] = $html;
				return $html;
			} else {
				$cache[$file] = false;
				$cache[$url] = false;
				return false;
			}
		}
	}
}

if (!function_exists('file_or_download')) {
	function file_or_download($file)
	{
		$contents = file_get_contents_or_download($file);
		$contents = str_replace("\n\r", "\n", $contents);
		$contents = str_replace("\r", "\n", $contents);
		$contents = str_replace("\n", "\r\n", $contents);
		$contents = explode("\r\n", $contents);
		return $contents;
	}
}

if (!function_exists('file_get_contents_with_trim')) {
	function file_get_contents_with_trim($file)
	{
		$data = file_get_contents_or_null($file);
		if ($data == null) {
			return $data;
		} else {
			return trim($data);
		}
	}
}

if (!function_exists('is_redirect')) {
	function is_redirect($data)
	{
		if (!is_string($data) || empty($data)) {
			return false;
		}
		// Check for meta http-equiv refresh redirect
		if (preg_match('/<meta\s+http-equiv\s*=\s*["\']refresh["\']/i', $data)) {
			return true;
		}
		return false;
	}
}

if (!function_exists('file_put_contents_check_for_errors')) {
	function file_put_contents_check_for_errors($filename, $data)
	{
		// patch
		create_path(dirname($filename));

		// note: check correct valid disk name
		if (is_dir($filename)) {
			trigger_error('is directory');
			return false;
		} elseif (str_contains(basename($filename), ' ')) {
			trigger_error('filename have spaces');
			return false;
		} elseif (str_contains(basename($filename), '_.html') && $filename != '__.html') {
			trigger_error('have "_" at the end');
			return false;
		} elseif (trim($data) == null) {
			trigger_error('is null');
			return false;
		} elseif (function_exists('is_broken_html') && is_broken_html($data, true)) {
			return false;  // no log here to call log from above
		} elseif (is_redirect($data)) {
			trigger_error('have redirect');
			return false;
		} else {
			if (file_put_contents($filename, $data, LOCK_SH)) {
				return true;
			} else {
				trigger_error('fail to write');
				return false;
			}
		}
	}
}

if (!function_exists('file_put_contents_and_backup')) {
	function file_put_contents_and_backup($filename, $data)
	{
		// patch
		create_path(dirname($filename));

		// vars
		$ext = get_extension($filename);
		$filename_no_ext = str_replace('.' . $ext, '', $filename);
		$today = date('ymd');
		$backup = $filename_no_ext . '_' . $today . '.' . $ext;
		$previous_data = file_get_contents($filename);

		// if file not exist backup not exist
		if (!file_exists($filename)) {
		} else {
			// create backup
			if (!file_exists($backup)) {
				file_put_contents($backup, $previous_data, LOCK_SH);
			}
		}

		// write the file
		if (file_put_contents($filename, $data, LOCK_SH)) {
			return true;
		} else {
			trigger_error('fail to write');
			return false;
		}
	}
}

if (!function_exists('file_put_contents_force')) {
	function file_put_contents_force($filename, $data)
	{
		// patch
		create_path(dirname($filename));

		// write
		if (file_put_contents($filename, $data)) {
			return true;
		} else {
			return false;
		}
	}
}



if (!function_exists('glob_or_download')) {
	function glob_or_download($pattern)
	{

		//work only if pattern is on basename and match an extension
		$files = array();
		$dirname = dirname($pattern);

		//first check if dirname exists means we have not download on our project
		$url = path2url($dirname);
		$ext = get_extension($pattern);

		//get html
		$page_for_download = url2path($url);
		$html = file_get_contents_or_download($page_for_download); //this will download or scan local
		if ($html == false) {

			return glob($pattern); //use traditional method if url not found
		}

		//get all links
		$links = get_all_links_from_html($html);
		foreach ($links as $link) {

			$ext_of_link = get_extension($link);
			if ($ext_of_link == $ext) {

				if (!is_full_url($link)) {
					$link = rel2abs($link, $url);
				}
				$link_disk = url2path($link);

				//before download check if exists
				if (file_exists($link_disk)) {

					$files[] = $link_disk;
				} else {

					$tmp = sys_get_temp_dir() . "/" . basename($link_disk);
					$link_contents = file_get_contents_curl($link);
					file_put_contents($tmp, $link_contents);
					$files[] = $tmp;
				}
			}
		}

		return $files;
	}
}

if (!function_exists('glob_with_generator')) {
	function glob_with_generator($files_prefix)
	{

		if (!str_contains($files_prefix, "*")) {

			trigger_error('prefix is not compatible');
			return array();
		}

		//Separate path from prefix, prefix will not work here
		$path = explode('*', $files_prefix);
		$path = $path[0];

		//ήταν έτσι - with error handling for inaccessible directories
		$directoryIterator = new RecursiveDirectoryIterator(
			$path,
			RecursiveDirectoryIterator::SKIP_DOTS | FilesystemIterator::SKIP_DOTS
		);
		$iterator = new RecursiveIteratorIterator(
			$directoryIterator,
			RecursiveIteratorIterator::SELF_FIRST,
			RecursiveIteratorIterator::CATCH_GET_CHILD // Skip directories that cause errors
		);

		foreach ($iterator as $file) {
			try {
				if (!$file->isDir()) {

					//patch gα αρχεια php
					if (str_contains($files_prefix, ".php")) {

						if (!str_contains($file->getPathname(), ".php")) {
							continue;
						}
					}

					//patch gα αρχεια html
					if (str_contains($files_prefix, ".html")) {

						if (!str_contains($file->getPathname(), ".html", )) {
							continue;
						}
					}

					//use this yield generator that emulate the array, instead of return
					yield normalize_path($file->getPathname());
				}
			} catch (UnexpectedValueException $e) {
				// Skip inaccessible files/directories
				continue;
			}
		}
	}
}

if (!function_exists('glob_no_old_versions')) {
	function glob_no_old_versions($files_prefix)
	{
		$files = glob($files_prefix);
		$files_no_old_versions = array();
		foreach ($files as $file) {
			if (is_old_version($file)) {
			} else {
				$files_no_old_versions[] = $file;
			}
		}
		return $files_no_old_versions;
	}
}

if (!function_exists('glob_from_url')) {
	function glob_from_url($url)
	{

		// κάνε αυτό μήπως έχει κενά
		$url = trim($url);

		// convert it to path
		$file = url2path($url);

		// download if is not exist but what exist from online
		if (!file_exists($file) && is_working($url)) {
			$html = file_get_contents_curl($url);

			// download file
			file_put_contents_force($file, $html);
			return array($file);
		}

		// create null if not exist at all
		elseif (!file_exists($file)) {
			return array($file);
		}

		// if exists
		else {
			return array($file);
		}
	}
}

if (!function_exists('glob_from_txt')) {
	function glob_from_txt($file)
	{

		$output = [];
		$lines = file($file);
		foreach ($lines as $line) {
			$line = trim($line);
			if (empty($line) || strpos($line, '#') === 0 || !filter_var($line, FILTER_VALIDATE_URL)) {
				continue;
			}
			$output[] = url2path($line);
		}
		return $output;
	}
}

if (!function_exists('get_extension_from_mime_type')) {
	function get_extension_from_mime_type($mime_type)
	{
		$mime_map = [
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif',
			'image/webp' => 'webp',
			'image/bmp' => 'bmp',
			'image/tiff' => 'tiff',
			'image/svg+xml' => 'svg',
			'image/heic' => 'heic',
			'image/avif' => 'avif',
			'video/mp4' => 'mp4',
			'video/webm' => 'webm',
			'video/ogg' => 'ogv',
			'video/x-msvideo' => 'avi',
			'video/quicktime' => 'mov',
			'video/x-matroska' => 'mkv',
			'video/3gpp' => '3gp',
			'video/x-flv' => 'flv'
		];

		return isset($mime_map[$mime_type]) ? $mime_map[$mime_type] : false;
	}
}

if (!function_exists('copy_from_url')) {
	function copy_from_url($url, $destinationPath)
	{
		$ch = curl_init($url);
		$fp = fopen($destinationPath, 'wb');

		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);

		$success = curl_exec($ch);

		curl_close($ch);
		fclose($fp);

		return $success;
	}
}

if (!function_exists('get_hash_directory')) {
	function get_hash_directory($directory)
	{
		if (!is_dir($directory)) {
			return false;
		}
		$files = array();
		$dir = dir($directory);
		while (false !== ($file = $dir->read())) {
			if (
				$file != '.' and $file != '..'
			) {
				if (is_dir($directory . '/' . $file)) {
					$files[] = get_hash_directory($directory . '/' . $file);
				} else {
					$files[] = md5_file($directory . '/' . $file);
				}
			}
		}

		$dir->close();

		return md5(implode('', $files));
	}
}

if (!function_exists('delete_empty_directories')) {
	function delete_empty_directories($dir)
	{
		$isEmpty = true;

		// Get all items in the directory
		$items = glob($dir . '/*');

		foreach ($items as $item) {
			if (is_dir($item)) {
				// Recursively check subdirectories
				if (!delete_empty_directories($item)) {
					$isEmpty = false;
				}
			} else {
				// If there's a file, the directory is not empty
				$isEmpty = false;
			}
		}

		// If the directory is empty, delete it
		if ($isEmpty) {
			echo 'Deleting ' . $dir . PHP_EOL;
			if (!is_cli()) {
				echo "<br>";
				flush_all();
			}
			rmdir($dir);
		}

		return $isEmpty;
	}
}

///////////////////////// ENVIRONMENT FUNCTIONS //////////////////////////

if (!function_exists('is_cli')) {
	function is_cli()
	{
		if (defined('STDIN')) {
			return true;
		}

		if (php_sapi_name() === 'cli') {
			return true;
		}

		if (array_key_exists('SHELL', $_ENV)) {
			return true;
		}

		if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
			return true;
		}

		if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
			return true;
		}

		return false;
	}
}

if (!function_exists('is_localhost')) {
	function is_localhost()
	{
		$whitelist = array(
			'127.0.0.1',
			'::1',
			'192.168.1.28:7999',
			'localhost',
		);

		// όταν είναι empty σημάνει οτι είναι cli και θεωρούμε ότι ειναι localhost
		if (empty($_SERVER['REMOTE_ADDR']) or in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
			return true;
		} elseif (is_cli()) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('is_mobile')) {
	function is_mobile()
	{
		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}

		$mobile_agents = ['Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi'];

		foreach ($mobile_agents as $agent) {
			if (stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
				return true;
			}
		}

		return false;
	}
}

if (!function_exists('is_desktop')) {
	function is_desktop()
	{
		return !is_mobile();
	}
}

if (!function_exists('is_ajax')) {

	function is_ajax()
	{
		// Check if the request is an AJAX request
		$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

		// Check if the request is a browser request
		$is_browser = !$is_ajax && isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false;

		// Return true if it's an AJAX request, false if it's a browser request
		return $is_ajax ? true : ($is_browser ? false : true);  // set default to true to more save what browser will run
	}
}

if (!function_exists('is_iframe')) {
	function is_iframe()
	{
		if (!empty($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe') {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('is_ob')) {
	function is_ob()
	{
		$status = ob_get_status();
		if ($status['level'] >= 1) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('tiv')) {
	function tiv($image, $width)
	{
		// show image on terminal
		system("java ../java/TerminalImageViewer -w $width $image");
	}
}

if (!function_exists('tiv_all')) {
	function tiv_all($folder, $ext, $width)
	{
		// show image's on terminal
		system("java ../java/TerminalImageViewer -w $width $folder/*$ext");
	}
}

if (!function_exists('flush_all')) {
	function flush_all()
	{
		// get rid of output buffer entirely
		while (ob_get_level()) {
			ob_end_clean();
		}

		// show in browser now
		flush();
	}
}

if (!function_exists('getParam')) {
	function getParam($key)
	{
		// try first is get is available cli or not
		if (isset($_GET[$key])) {
			if ($_GET[$key] != null) {
				return $_GET[$key];
			} else {
				return true;
			}
		}

		// then try to get a option from cli
		elseif (php_sapi_name() == 'cli') {
			// https://www.php.net/manual/en/function.getopt.php
			$longopts = array($key . '::');  // No value
			$options = getopt('test', $longopts);
			if (array_key_exists($key, $options)) {
				if ($options[$key] != false) {
					return $options[$key];
				} else {
					return true;
				}
			}
		}
	}
}

if (!function_exists('get_initially_run_script')) {
	function get_initially_run_script()
	{
		$stack = debug_backtrace();
		$firstFrame = $stack[count($stack) - 1];
		$initialFile = $firstFrame['file'];
		return $initialFile;
	}
}

if (!function_exists('get_previous_run_script_2')) {
	function get_previous_run_script_2($current_running_file)
	{
		$included_files = get_included_files();
		$i = 0;
		foreach ($included_files as $file) {
			if ($file == $current_running_file) {
				break;
			}
			$i++;
		}
		return $included_files[$i - 1];
	}
}

if (!function_exists('get_ip_address')) {
	function get_ip_address()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			$ipaddress = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		} else if (getenv('HTTP_X_FORWARDED')) {
			$ipaddress = getenv('HTTP_X_FORWARDED');
		} else if (getenv('HTTP_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		} else if (getenv('HTTP_FORWARDED')) {
			$ipaddress = getenv('HTTP_FORWARDED');
		} else if (getenv('REMOTE_ADDR')) {
			$ipaddress = getenv('REMOTE_ADDR');
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}
}

if (!function_exists('reset_global_static_cache')) {
	function reset_global_static_cache()
	{
		$defined_vars = get_defined_vars();
		foreach ($defined_vars as $var => $value) {
			if (strpos($var, 'cache') !== false && is_array($value)) {
				reset($value);
			}
		}
	}
}

if (!function_exists('run_patches_to_windows_paths')) {
	function run_patches_to_windows_paths()
	{
		// note: to antikatastisame me to process mesa sto server php
		return;

		// αυτό είναι μόνο για wordpress και εξαρτάτε από το wordpress
		if (!defined('ABSPATH')) {
			return false;
		}

		// ξεκινάει
		if (isset($_SERVER)) {
			$_SERVER = str_replace_in_array('\\\\', '\\', $_SERVER);
		}
		if (isset($_GET)) {
			$_GET = str_replace_in_array('\\\\', '\\', $_GET);
		}

		if (isset($_COOKIES)) {
			$_COOKIES = str_replace_in_array('\\\\', '\\', $_COOKIES);
		}
		if (isset($_SESSION)) {
			$_SESSION = str_replace_in_array('\\\\', '\\', $_SESSION);
		}
		if (isset($_REQUEST)) {
			$_REQUEST = str_replace_in_array('\\\\', '\\', $_REQUEST);
		}
	}
}

if (!function_exists('reload_with_params')) {
	function reload_with_params($array = array(), $new_url = false, $clean_current_params = false, $html = false, $force_reload = false)
	{
		// αν είναι κωδικοποιημένο το url αποκωδικοποιήσετε συμβαίνει κυρίως αν προέρχεται μέσω get
		if ($new_url == true) {
			if (urlencode(urldecode($new_url)) === $new_url) {
				$new_url = urldecode($new_url);
			}
		}

		// generate the new url by the params
		$url = update_url_params($array, $new_url, $clean_current_params);
		$refresh = '<meta http-equiv="refresh" content="0;URL=' . $url . '" />';
		if (headers_sent()) {
			// If headers have been sent, echo the refresh meta tag
			echo $refresh;
		} else {
			// If headers have not been sent, set the Location header
			header("Location: " . $url);
			exit;
		}
	}
}

if (!function_exists('tryWordPressConfigFromFile')) {
	function tryWordPressConfigFromFile($configPath = 'wp-config.php')
	{
		// Check if config file exists
		if (!file_exists($configPath)) {
			return array();
		}

		// Read the config file content
		$content = file_get_contents($configPath);
		if ($content === false) {
			return array();
		}

		// Extract DB_NAME
		if (preg_match("/define\s*\(\s*'DB_NAME'\s*,\s*'([^']*)'\s*\)/", $content, $matches)) {
			$config['name'] = $matches[1];
		}

		// Extract DB_USER
		if (preg_match("/define\s*\(\s*'DB_USER'\s*,\s*'([^']*)'\s*\)/", $content, $matches)) {
			$config['user'] = $matches[1];
		}

		// Extract DB_PASSWORD
		if (preg_match("/define\s*\(\s*'DB_PASSWORD'\s*,\s*'([^']*)'\s*\)/", $content, $matches)) {
			$config['pass'] = $matches[1];
		}

		// Extract DB_HOST
		if (preg_match("/define\s*\(\s*'DB_HOST'\s*,\s*'([^']*)'\s*\)/", $content, $matches)) {
			$config['host'] = $matches[1];
		}

		// Extract table prefix
		if (preg_match("/\\\$table_prefix\s*=\s*'([^']*)'/", $content, $matches)) {
			$config['table_prefix'] = $matches[1];
		}

		// Extract WP_SITEURL if defined
		if (preg_match("/define\s*\(\s*'WP_SITEURL'\s*,\s*'([^']*)'\s*\)/", $content, $matches)) {
			$config['site_url'] = $matches[1];
		}

		return $config;
	}
}

if (!function_exists('check_url_matches_any_post')) {
	function check_url_matches_any_post($url = false, $db_config = array())
	{

		// If wordpress not found, then dont continue
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php')) {
			return false;
		}

		static $cache = [];

		// if url is false get it from request uri
		if ($url == false) {
			$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}

		// Check if the result is already cached
		if (isset($cache[$url])) {
			return $cache[$url];
		}

		try {
			// Normalize input URL
			$clean_url = urldecode($url);
			$clean_url = str_replace("http://www.", "", $clean_url);
			$clean_url = str_replace("https://www.", "", $clean_url);
			$clean_url = str_replace("http://", "", $clean_url);
			$clean_url = str_replace("https://", "", $clean_url);
			$clean_url = trim($clean_url, '/');

			// If path of url is /index.php then is true
			$test_1 = $_SERVER['HTTP_HOST'] . '/index.php';
			$test_2 = $_SERVER['HTTP_HOST'];
			if ($clean_url == $test_1 || $clean_url == $test_2) {
				$cache[$url] = true;
				return true;
			}

			// Default MySQL config
			$default_config = [
				'host' => 'localhost',
				'name' => 'wordpress',
				'user' => 'root',
				'pass' => '',
				'table_prefix' => 'wp_',
				'site_url' => isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : 'http://localhost'
			];

			// Try to get wordpress config from wp-config.php when it can
			$db_config_try = tryWordPressConfigFromFile($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');

			// Merge configs
			$config = array_merge($default_config, $db_config_try, $db_config);

			// Set SQLite path
			$sqlite_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/database/.ht.sqlite';

			// Check if PDO extension is loaded
			if (!extension_loaded('pdo')) {
				$cache[$url] = false;
				return false;
			}

			// Try SQLite
			$pdo = null;
			if (extension_loaded('pdo_sqlite') && file_exists($sqlite_path)) {
				$pdo = new PDO('sqlite:' . $sqlite_path);
			} elseif (extension_loaded('pdo_mysql')) {
				$pdo = new PDO(
					"mysql:host={$config['host']};dbname={$config['name']}",
					$config['user'],
					$config['pass']
				);
			} else {
				$cache[$url] = false;
				return false;
			}
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Check cp_editor meta
			$meta_sql = "SELECT post_id, meta_value
	FROM {$config['table_prefix']}postmeta
	WHERE meta_key = 'cp_editor'";
			$meta_results = $pdo->query($meta_sql)->fetchAll(PDO::FETCH_ASSOC);

			foreach ($meta_results as $meta) {
				if (empty($meta['meta_value'])) {
					continue;
				}
				$meta_url = $config['site_url'] . '/' . $meta['meta_value'];
				$meta_url = urldecode($meta_url);
				$meta_url = preg_replace(
					['(^https?://)', '/^www\./', '/\/$/'],
					'',
					strtolower($meta_url)
				);
				if ($meta_url === $clean_url) {
					$cache[$url] = true;
					return true;
				}
			}

			// Posts query
			$posts_sql = "WITH RECURSIVE parent_tree AS (
	SELECT
	ID,
	post_name,
	post_parent,
	post_type,
	post_status,
	post_name as path
	FROM {$config['table_prefix']}posts
	WHERE post_parent = 0

	UNION ALL

	SELECT
	p.ID,
	p.post_name,
	p.post_parent,
	p.post_type,
	p.post_status,
	pt.path || '/' || p.post_name as path
	FROM {$config['table_prefix']}posts p
	JOIN parent_tree pt ON pt.ID = p.post_parent
	)
	SELECT
	ID,
	post_type,
	path as full_path
	FROM parent_tree
	WHERE post_status = 'publish'";
			$stmt = $pdo->prepare($posts_sql);
			$stmt->execute();
			$posts_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach ($posts_results as $post) {
				$post_url = urldecode($config['site_url'] . '/' . $post['full_path']);
				$clean_post_url = preg_replace(
					['(^https?://)', '/^www\./', '/\/$/'],
					'',
					strtolower($post_url)
				);
				if ($clean_url === $clean_post_url) {
					$cache[$url] = true;
					return true;
				}
			}
		} catch (Exception $e) {
			$cache[$url] = false;
			return false;
		}

		$cache[$url] = false;
		return false;
	}
}

if (!function_exists('is_user_admin_back_end')) {
	function is_user_admin_back_end($cookies_only = false)
	{

		// Skip google crawler
		if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot")) {
			return false;
		}

		// WordPress admin checks - inside plugins or themes
		if (!$cookies_only && function_exists('current_user_can') && current_user_can('manage_options')) {
			return true;
		}

		// WordPress admin checks - old inside core
		if (!$cookies_only && function_exists('is_user_admin_back_end_wordpress_db') && is_user_admin_back_end_wordpress_db()) {
			return true;
		}

		// JSON-based authentication
		$db_file = $_SERVER["DOCUMENT_ROOT"] . '/users.json';
		if (file_exists($db_file)) {
			$db = json_decode(file_get_contents($db_file), true);

			// Username and password validation
			$formusername = null;
			$formpasswd = null;

			if (!$cookies_only && isset($_GET["username"]) && $_GET["username"] != 'null' && isset($_GET["passwd"]) && $_GET["passwd"] != 'null' && $_GET["passwd"] != null) {
				$formusername = strtolower(rawurldecode($_GET["username"]));
				$formpasswd = rawurldecode($_GET["passwd"]);
			} elseif (!$cookies_only && isset($_POST["username"]) && $_POST["username"] != 'null' && isset($_POST["passwd"]) && $_POST["passwd"] != 'null' && $_POST["passwd"] != null) {
				$formusername = strtolower(rawurldecode($_POST["username"]));
				$formpasswd = rawurldecode($_POST["passwd"]);
			} elseif (isset($_COOKIE["username"]) && $_COOKIE["username"] != 'null' && isset($_COOKIE["passwd"]) && $_COOKIE["passwd"] != 'null' && $_COOKIE["passwd"] != null) {
				$formusername = strtolower(rawurldecode($_COOKIE["username"]));
				$formpasswd = rawurldecode($_COOKIE["passwd"]);
			} else {
				return false;
			}

			static $results = [];
			$ip = $_SERVER['REMOTE_ADDR'];
			$attempt_key = hash('sha256', $ip . "\0" . $formusername . "\0" . $formpasswd);
			if (isset($results[$attempt_key])) {
				return $results[$attempt_key];
			}

			$file = fopen($_SERVER['DOCUMENT_ROOT'] . '/login-attempts.txt', 'c+');
			if ($file === false) {
				throw new RuntimeException('Cannot open login-attempts.txt');
			}
			try {
				// Serialize the password comparison and the shared IP counter update.
				if (!flock($file, LOCK_EX)) {
					throw new RuntimeException('Cannot lock login-attempts.txt');
				}
				$content = stream_get_contents($file);
				if ($content === false) {
					throw new RuntimeException('Cannot read login-attempts.txt');
				}
				$data = [];
				$now = time();
				foreach (explode("\n", trim($content)) as $line) {
					if ($line === '') {
						continue;
					}
					$parts = explode(' | ', trim($line));
					if (count($parts) !== 2 || !preg_match('/^[0-9]+(?:,[0-9]+)*$/D', $parts[1])) {
						throw new RuntimeException('Invalid format in login-attempts.txt');
					}
					$address = $parts[0];
					foreach (explode(',', $parts[1]) as $timestamp) {
						if ((int) $timestamp > $now - 300) {
							$data[$address][] = (int) $timestamp;
						}
					}
				}
				
				$attempts = $data[$ip] ?? [];
				$retry_after = count($attempts) >= 5 ? min($attempts) + 300 - $now : 0;
				$valid = false;
				$user_info = null;
				if ($retry_after === 0) {
					if (isset($db['users'][$formusername]['info']) && is_array($db['users'][$formusername]['info'])) {
						$user_info = current($db['users'][$formusername]['info']);
						$valid = isset($user_info['passwd']) && $user_info['passwd'] != null && $formpasswd == $user_info['passwd'];
					}
				}
				if ($retry_after === 0 && !$valid) {
					$data[$ip][] = time();
				}
				$lines = [];
				foreach ($data as $address => $times) {
					$lines[] = $address . ' | ' . implode(',', $times);
				}
				$attempt_text = implode("\n", $lines);
				if ($attempt_text !== '') {
					$attempt_text .= "\n";
				}
				if (!rewind($file) || !ftruncate($file, 0) || fwrite($file, $attempt_text) !== strlen($attempt_text) || !fflush($file)) {
					throw new RuntimeException('Cannot save login-attempts.txt');
				}
			} finally {
				fclose($file);
			}

			if ($retry_after > 0) {
				http_response_code(429);
				header('Retry-After: ' . $retry_after);
				trigger_error('Πολλά αιτήματα. Προσπαθήστε ξανά σε ' . $retry_after . ' δευτερόλεπτα.', E_USER_WARNING);
				exit;
			}
			if (!$valid) {
				$results[$attempt_key] = false;
				return false;
			}

			$user_level = isset($user_info['level']) ? trim($user_info['level']) : null;
			$results[$attempt_key] = $user_level != null;
			return $results[$attempt_key];
		}
		return false;
	}
}

if (!function_exists('is_inside_any_admin')) {
	function is_inside_any_admin($url = false)
	{
		if ($url == true) {
		} else {
			$url = get_current_url();
		}

		$path = url2path($url);

		// search if is on wordpress core
		if (strpos($path, '/wp-admin') !== false) {
			return true;
		}

		if (strpos($path, '/wp-login') !== false) {
			return true;
		}

		//  search if is on common core
		if (strpos($path, '/common') !== false) {
			return true;
		}
		return false;
	}
}

if (!function_exists('replace_last_occurrence')) {

	function replace_last_occurrence($haystack, $needle, $replace)
	{
		$pos = strrpos($haystack, $needle);
		if ($pos !== false) {
			return substr_replace($haystack, $replace, $pos, strlen($needle));
		}
		return $haystack;
	}
}

if (!function_exists('push_admin_link_on_html')) {
	function push_admin_link_on_html($html, $put, $basename_of_file_check_if_is_putted)
	{
		if (str_contains($html, $put) && $put != null) {

			return $html;
		} elseif (str_contains($html, "\"floating-tools-div\">")) {

			$call_previous_puts = str_between($html, "\"floating-tools-div\">", "</div>");
			$search = "\"floating-tools-div\">" . $call_previous_puts . "</div>";
			$replace = "\"floating-tools-div\">" . $put . $call_previous_puts . "</div>";
			$ids = "<span style=\"display:none\">" . $basename_of_file_check_if_is_putted . "</span>" . PHP_EOL;
			$html = replace_last_occurrence($html, "</body>", $ids . PHP_EOL . "</body>");
			$html = str_replace($search, $replace, $html);
		} else {

			//put that on links for wordpress changes my emojy to wordpress emojies
			$put = "<style>#floating-tools-div > a > small > img {width: 0.4em !important;height: 0.4em !important}</style><div style=\"background:transparent;display: inline-block;position: fixed;bottom: 0;right: 0;z-index: 9999999999;text-align: right;padding:6px\" id=\"floating-tools-div\">$put</div>";
			$ids = "<span style=\"display:none\">" . $basename_of_file_check_if_is_putted . "</span>" . PHP_EOL;
			$html = replace_last_occurrence($html, "</body>", $put . PHP_EOL . "</body>");
			$html = replace_last_occurrence($html, "</body>", $ids . PHP_EOL . "</body>");
		}
		return $html;
	}
}

if (!function_exists('attach_admin_link')) {
	function attach_admin_link($url, $text, $color, $html, $symbol = false)
	{

		if ($symbol == false) {
			$symbol = "&#128302";
		}
		$put = "<a class=\"cms-link\" style=\"text-decoration:none;color:$color;text-shadow: 1px 1px white;\" href=" . $url . "><small style=\"font-size:12pt\">$text $symbol</small></a><br>";
		$html = push_admin_link_on_html($html, $put, slugify($text));
		return $html;
	}
}

if (!function_exists('is_user_admin_back_end_wordpress_db')) {
	/**
	 * @deprecated Ξεπερασμένος έλεγχος admin: εμπιστεύεται το username του cookie
	 * χωρίς να επαληθεύει την υπογραφή, τη λήξη ή το session token του WordPress.
	 * TODO: Αντικατάσταση με επαλήθευση του cookie μέσω wp_validate_auth_cookie()
	 * και έλεγχο manage_options για τον επαληθευμένο χρήστη μέσω user_can().
	 * TODO: Όταν δεν έχει φορτωθεί το WordPress, να μην παραχωρούνται δικαιώματα
	 * μόνο από το username του cookie και την εγγραφή capabilities στη βάση.
	 */
	function is_user_admin_back_end_wordpress_db($db_config = array())
	{
		// If wordpress not found, then dont continue
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php')) {
			return false;
		}

		static $cached_is_admin = null;
		if ($cached_is_admin !== null) {
			return $cached_is_admin;
		}

		// Find cookie
		$cookie_name = null;
		foreach ($_COOKIE as $key => $value) {
			if (strpos($key, 'wordpress_logged_in_') === 0) {
				$cookie_name = $key;
				break;
			}
		}
		if (!$cookie_name) {
			$cached_is_admin = false;
			return false;
		}

		// Parse user from cookie
		$parts = explode('|', $_COOKIE[$cookie_name]);
		if (empty($parts[0])) {
			$cached_is_admin = false;
			return false;
		}
		$username = $parts[0];

		try {
			// Default config & wp-config
			$default_config = [
				'host' => 'localhost',
				'name' => 'wordpress',
				'user' => 'root',
				'pass' => '',
				'table_prefix' => 'wp_',
			];
			$db_config_try = tryWordPressConfigFromFile($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');
			$config = array_merge($default_config, $db_config_try, $db_config);

			// PDO connect
			$sqlite_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/database/.ht.sqlite';
			if (!extension_loaded('pdo')) {
				$cached_is_admin = false;
				return false;
			}
			if (extension_loaded('pdo_sqlite') && file_exists($sqlite_path)) {
				$pdo = new PDO('sqlite:' . $sqlite_path);
			} elseif (extension_loaded('pdo_mysql')) {
				$pdo = new PDO(
					"mysql:host={$config['host']};dbname={$config['name']}",
					$config['user'],
					$config['pass']
				);
			} else {
				$cached_is_admin = false;
				return false;
			}
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Get user ID from wp_users
			$table_users = $config['table_prefix'] . 'users';
			$sql_user = "SELECT ID FROM {$table_users} WHERE user_login = :username LIMIT 1";
			$stmt = $pdo->prepare($sql_user);
			$stmt->execute(['username' => $username]);
			$user_id = $stmt->fetchColumn();
			if (!$user_id) {
				$cached_is_admin = false;
				return false;
			}

			// Check capabilities
			$table_usermeta = $config['table_prefix'] . 'usermeta';
			$sql_meta = "SELECT meta_value FROM {$table_usermeta}
	WHERE user_id = :uid AND meta_key LIKE '%capabilities'";
			$stmt = $pdo->prepare($sql_meta);
			$stmt->execute(['uid' => $user_id]);
			$meta_value = $stmt->fetchColumn();
			if ($meta_value && strpos($meta_value, 'administrator') !== false) {
				$cached_is_admin = true;
				return true;
			}
		} catch (Exception $e) {
			$cached_is_admin = false;
			return false;
		}

		$cached_is_admin = false;
		return false;
	}
}

/**
 * Check if a directory path should be skipped (excluded from scanning)
 * 
 * @param string $path The directory path to check
 * @return bool True if the directory should be skipped, false otherwise
 */
if (!function_exists('should_skip_directory')) {
	function should_skip_directory($path)
	{
		// Normalize path separators
		$path = str_replace('\\', '/', $path);
		$path_lower = strtolower($path);

		// List of directories to skip
		$skip_dirs = [
			// Version control
			'.git',
			'.svn',
			'.hg',
			'.bzr',
			'.github',
			// Package managers
			'node_modules',
			'vendor',
			'venv',
			'.venv',
			'env',
			'.env',
			'virtualenv',
			// Python
			'__pycache__',
			'.pytest_cache',
			'.mypy_cache',
			'dist',
			'build',
			'*.egg-info',
			// Node
			'npm_cache',
			'yarn_cache',
			'.cache',
			// Windows system
			'$Recycle.Bin',
			'System Volume Information',
			'pagefile.sys',
			'hiberfil.sys',
			'.windows',
			'AppData',
			'Program Files',
			'Program Files (x86)',
			'Windows',
			'ProgramData',
			// Linux/Unix system
			'/proc',
			'/sys',
			'/dev',
			'/boot',
			'/etc',
			'/var/log',
			'/var/cache',
			'/lost+found',
			'/tmp',
			'/root',
			'etc',
			'proc',
			'sys',
			'dev',
			'boot',
			'var',
			// macOS
			'.Trash',
			'.Spotlight-V100',
			'.TemporaryItems',
			'Library',
			'Applications',
			// Android
			'Android',
			'.android',
			'.gradle',
			// IDE/Editor
			'.vscode',
			'.idea',
			'.sublime',
			'.eclipse',
			'.netbeans',
			'node_modules/.bin',
			'.npm',
			// Logs and temp
			'logs',
			'log',
			'temp',
			'tmp',
			'.temp',
			// Docker/Container
			'.docker',
			'.container',
			// Archive/Backup
			'.zip',
			'.rar',
			'.tar',
			'.bak',
			// Custom
			'streetview',
			'countries',
			'wfb',
			'wfbcurrent',
			'geographic_names',
			'noms_geographiques',
			'noms_geographiques',
			'nombres_geograficos',
			'global_weather',
			'wp-includes',
			'wp-admin',
			'downloads',
			'download',
			'uploads',
			'cache',
			'data',
			'database',
			'backups',
			'websites-backups', //dont put users here because i skip all windows dirs
		];

		foreach ($skip_dirs as $skip_dir) {
			// Match exact directory names (case-insensitive)
			if (
				stripos($path, '/' . $skip_dir . '/') !== false ||
				stripos($path, '\\' . $skip_dir . '\\') !== false ||
				strtolower(basename($path)) === strtolower($skip_dir)
			) {
				return true;
			}
		}

		return false;
	}
}

// Load environment variables from .env file
if (!function_exists('load_enviroment_variables')) {
	function load_enviroment_variables($path)
	{
		if (!file_exists($path)) {
			return false;
		}

		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $i => $line) {
			// Strip UTF-8 BOM from the first line if present
			if ($i === 0) {
				$line = ltrim($line, "\xEF\xBB\xBF");
			}

			// Skip comments
			if (strpos(trim($line), '#') === 0) {
				continue;
			}

			// Parse KEY=VALUE
			if (strpos($line, '=') !== false) {
				list($key, $value) = explode('=', $line, 2);
				$key = trim($key);
				$value = trim($value);

				// Set environment variables
				if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
					putenv(sprintf('%s=%s', $key, $value));
					$_ENV[$key] = $value;
					$_SERVER[$key] = $value;
				}
			}
		}
		return true;
	}
}
