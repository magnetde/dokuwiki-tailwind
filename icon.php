<?php

/**
 * PHP script to determine icons.
 * This script can be called by requesting /lib/tpl/tailwind/icon.php?icon=NAME
 * Furthermore, the query parameters "color", "width" and "height" are supported.
 */

require_once dirname(__FILE__) . '/inc/icon/icons.php'; // Include the icon map
require_once dirname(__FILE__) . '/inc/icon/colors.php'; // Include the colors

global $ICONS, $UNKNOWN_ICON;

/**
 * Wrapper around htmlspecialchars(), copied from the DokuWiki source code.
 * (See inc/common.php)
 */
function hsc($string) {
	return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
}

$icon = hsc($_GET['icon']);

if(empty($icon) || !array_key_exists($icon, $ICONS))
	$icon = $UNKNOWN_ICON;
else
	$icon = $ICONS[$icon];

$width  = '1em';
$height = '1em';
$fill   = 'currentColor';

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

if($_GET['color'])
	$fill = parseColor(hsc($_GET['color']));

$path    = sprintf('<path fill="%s" d="%s"/>', $fill, $icon);
$svg     = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="%s" height="%s" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">%s</svg>';
$content = sprintf($svg, $width, $height, $path);

$content_type = 'image/svg+xml; charset=utf-8';
header("Content-Type: $content_type");
header("Cache-Control: max-age=2592000"); // 30days (60sec * 60min * 24hours * 30days)

echo $content;
