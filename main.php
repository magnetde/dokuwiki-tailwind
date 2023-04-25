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

// must be run from within DokuWiki
@require_once dirname(__FILE__) . '/inc/global.php';

$showSidebar = page_findnearest($conf['sidebar']) && ($ACT == 'show');
?>

<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<link rel="apple-touch-icon" sizes="64x64" href="<?php echo tpl_basedir(); ?>images/apple-touch-icon.png">
		<link rel="icon" type="image/x-icon" href="<?php echo tpl_basedir(); ?>images/favicon.ico">
		<title>
			<?php tpl_pagetitle() ?> - <?php echo strip_tags($conf['title']) ?>
		</title>
		<?php
		tpl_metaheaders();
		echo tpl_favicon(array(
			'favicon',
			'mobile',
		))
		?>

		<?php tpl_includeFile('meta.html') ?>

		<!-- CSS file -->
		<!-- It is not possible to provide with "style.ini", because the CSS syntax is not compatible with the CSS parser used by DokuWiki. -->
		<!-- See: https://github.com/tailwindlabs/tailwindcss/issues/7121 -->
		<link href="<?php echo tpl_basedir(); ?>assets/css/main.css" rel="stylesheet" />

		<!-- JS -->
		<!-- Do not provide it with "script.js". -->
		<script src="<?php echo tpl_basedir(); ?>assets/js/flowbite.min.js" type="text/javascript"></script>
	</head>

	<?php tpl_flush() ?>

	<body class="dark:bg-gray-900 antialiased">

		<!--- Navbar --->
		<?php require_once('tpl/navbar.php'); ?>

		<?php
		// Render the content initially
		ob_start();
		tpl_content(false);
		$content = ob_get_clean();
		?>

		<!--- Main container --->
		<div class="w-full z-10 px-4 mx-auto max-w-10xl lg:flex">

			<!--- Left sidebar --->
			<aside class="fixed inset-0 z-20 flex-none hidden h-full w-72 lg:static lg:h-auto lg:overflow-y-visible lg:pt-0 lg:w-64 lg:block">
				<!--- TODO --->
				left (mt-4 needed for first item)
			</aside>

			<!--- Middle content and left sidebar --->
			<main class="flex w-full min-w-0 lg:static lg:max-h-full lg:overflow-visible">

				<!--- Main content --->
				<?php require_once('tpl/content.php'); ?>

				<!--- Right sidebar --->
				<div class="flex-none hidden w-72 pl-8 mr-8 xl:text-sm xl:block">
					<!--- TODO --->
					right
				</div>

			</main>
		</div>

		<div class="no">
			<?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?>
		</div>
	</body>
</html>
