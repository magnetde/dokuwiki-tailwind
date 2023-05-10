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
		<script src="<?php echo tpl_basedir(); ?>assets/js/bootstrap-scrollspy.min.js" type="text/javascript"></script>
	</head>

	<?php tpl_flush() ?>

	<body class="dark:bg-gray-900 antialiased" data-bs-spy="scroll" data-bs-target="#dw__toc" data-bs-offset="120">

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
			<aside class="fixed inset-0 z-20 flex-none hidden h-full lg:w-56 lg:static lg:h-auto lg:overflow-y-visible lg:pt-0 lg:block xl:w-72">
				<div class="overflow-y-auto sticky top-[theme(height.navbar)] h-[calc(100vh-theme(height.navbar)-2px)] flex-col pt-10 pb-6 pr-6">
					<?php
					if($showSidebar) {
						echo '<div class="dw-sidebar prose prose-sm dark:prose-invert">';
						tpl_includeFile('sidebarheader.html');
						tpl_include_page($conf['sidebar'], true, true);
						tpl_includeFile('sidebarfooter.html');
						echo '</div>';
					}
					?>
				</div>
			</aside>

			<!--- Middle content and left sidebar --->
			<main class="flex w-full min-w-0 lg:static lg:max-h-full lg:overflow-visible">

				<!--- Main content --->
				<?php require_once('tpl/content.php'); ?>

				<!--- Right sidebar --->
				<div class="flex-none hidden w-72 pl-8 xl:text-sm xl:block">
					<div class="flex overflow-y-auto sticky top-[theme(height.navbar)] h-[calc(100vh-theme(height.navbar)-2px)] flex-col pt-10 pb-6">
						<?php
						$toc = _tpl_getTOC();
						if($toc) {
							echo '<h4 class="mb-4 pl-4 font-semibold text-gray-900 dark:text-white">'
								.$lang['toc']
								.'</h4>';
							echo $toc;
						}
						?>
					</div>
				</div>

			</main>
		</div>

		<div class="no">
			<?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?>
		</div>
	</body>
</html>
