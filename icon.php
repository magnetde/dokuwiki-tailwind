<?php

/**
 * PHP script to determine icons.
 * This script can be called by requesting /lib/tpl/tailwind/icon.php?icon=NAME
 * Furthermore, the query parameters "color", "width" and "height" are supported.
 */

@require_once dirname(__FILE__) . '/inc/icons.php'; // Include the icon map

global $ICONS;

/**
 * Wrapper around htmlspecialchars(), copied from the DokuWiki source code.
 * (See inc/common.php)
 */
function hsc($string) {
	return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
}

$icon = hsc($_GET['icon']);

if(empty($icon) || !array_key_exists($icon, $ICONS)) {
	header('Content-Type: text/plain; charset=utf-8', true);
	http_response_code(404);
	print "Not Found";
	exit;
}

$width  = '1em';
$height = '1em';
$fill   = '';

if($_GET['width']) {
	$param = hsc($_GET['width']);
	$width  = $param;
	$height = $param;
}

if($_GET['height']) {
	$param = hsc($_GET['height']);
	$width  = $param;
	$height = $param;
}

if($_GET['color']) {
	$param = hsc($_GET['color']);
	$fill = $param;
}

$body = $ICONS[$icon];

if($fill) {
	$body = str_replace('currentColor', $fill, $body);
}

$svg     = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="%s" height="%s" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">%s</svg>';
$content = sprintf($svg, $width, $height, $body);

$content_type = 'image/svg+xml; charset=utf-8';
header("Content-Type: $content_type");
echo $content;
