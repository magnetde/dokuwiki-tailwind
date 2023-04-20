<!--
=========================================================
*  TailwindCSS Dokuwiki Template
*  Created with TailwindCSS and Flowbite.
=========================================================
-->

<?php
if(!defined('DOKU_INC')) {
	die();
}

/* must be run from within DokuWiki */
@require_once dirname(__FILE__) . '/inc/tpl_functions.php'; /* include hook for template functions */

$showTools = !tpl_getConf('hideTools') || (tpl_getConf('hideTools') && !empty($_SERVER['REMOTE_USER']));
$showSidebar = page_findnearest($conf['sidebar']) && ($ACT == 'show');
?>

<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="apple-touch-icon" sizes="64x64" href="<?php echo tpl_basedir(); ?>images/apple-touch-icon.png">
		<link rel="icon" type="image/x-icon" href="<?php echo tpl_basedir(); ?>images/favicon.ico">
		<title>
			<?php tpl_pagetitle()?> - <?php echo strip_tags($conf['title']) ?>
		</title>
		<?php
        tpl_metaheaders();
        echo tpl_favicon(array(
			'favicon',
			'mobile',
		))
		?>

		<?php tpl_includeFile('meta.html')?>

		<!--- TODO: add styles and scripts --->
	</head>

	<body class="docs" data-spy="scroll" data-target="#dw__toc" data-offset="10">
		<!--- TODO --->
	</body>
</html>
