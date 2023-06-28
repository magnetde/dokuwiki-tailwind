<?php

// must be run from within DokuWiki
if(!defined('DOKU_INC'))
	die();

@require_once dirname(__FILE__) . '/inc/global.php';

// Set the content variable
ob_start();
require_once dirname(__FILE__) . '/inc/ui/mediadetails.php';
$content = ob_get_clean();

// HTML content
require_once dirname(__FILE__) . '/inc/ui/main.php';
?>
