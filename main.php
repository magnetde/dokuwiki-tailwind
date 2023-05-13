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
		<div class="w-full lg:flex">

			<!--- Left sidebar --->
			<?php if($showSidebar): ?>
			<aside class="border-r border-gray-900/10 dark:border-gray-50/[0.06] hidden lg:block lg:w-sidebar-lg 2xl:w-sidebar-2xl">
				<div class="pt-10 px-6 sticky overflow-y-auto top-[theme(height.navbar)] h-[calc(100vh-theme(height.navbar)-1px)] lg:w-sidebar-lg 2xl:w-sidebar-2xl">
					<div class="dw-sidebar prose prose-sm 2xl:prose-base dark:prose-invert">
					<?php
					tpl_includeFile('sidebarheader.html');
					tpl_include_page($conf['sidebar'], true, true);
					tpl_includeFile('sidebarfooter.html');
					?>
					</div>
				</div>
			</aside>
			<?php endif ?>

			<!--- Middle content and left sidebar --->
			<main class="w-full lg:flex lg:flex-auto lg:overflow-x-hidden xl:overflow-x-initial">
				<div class="mx-auto my-0 flex">

					<!--- Main content --->
					<div class="<?php echo clsx(
						'block w-full lg:w-content-lg xl:w-content-xl 2xl:w-content-2xl',

						// If the sidebar exists and the ToC is still not displayed (screen size = lg),
						// then a margin is added at the right, that matches the width of the ToC to center the main content.
						// If the sidebar does not exists and the ToC is displayed (screen size >= xl),
						// a margin is added at the left, that matches the width of the ToC to center the main content.
						$showSidebar ?
						'mr-0 lg:mr-[theme(width.sidebar-lg)] xl:mr-0' :
						'ml-0 xl:ml-[theme(width.sidebar-lg)] 2xl:ml-[theme(width.sidebar-2xl)]',
					) ?>">
						<?php require_once('tpl/content.php'); ?>
					</div>

					<!--- Right sidebar --->
					<div class="flex-none align-top hidden pl-8 text-sm 2xl:text-base xl:block xl:w-sidebar-lg 2xl:w-sidebar-2xl">
						<div class="flex overflow-y-auto sticky top-[theme(height.navbar)] h-[calc(100vh-theme(height.navbar)-1px)] flex-col pt-10 pb-6">
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

				</div>
			</main>
		</div>

		<div class="absolute w-0 h-0">
			<?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?>
		</div>
	</body>
</html>
