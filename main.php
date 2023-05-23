<!--
=========================================================
*  TailwindCSS Dokuwiki Template
*  Created with TailwindCSS and Flowbite.
=========================================================
-->

<?php

// must be run from within DokuWiki
if(!defined('DOKU_INC'))
	die();

@require_once dirname(__FILE__) . '/inc/global.php';

// Render the content initially
ob_start();
tpl_content(false);
$content = ob_get_clean();

// HTML content
require_once dirname(__FILE__) . '/inc/ui/html.php';
?>
