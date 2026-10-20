<?php
if(!defined('DOKU_INC'))
	die();

// Show sidebar at pages (if exists) or if the media manager is opened.
$showSidebar = (page_findnearest($conf['sidebar']) && $ACT == 'show') || ($ACT == 'media');
$toc = _tpl_getTOC();
?>

<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>">
<head>
	<?php require_once('meta.php'); ?>
</head>

<?php tpl_flush() ?>

<body class="dark:bg-gray-900 antialiased" data-bs-spy="scroll" data-bs-target="#dw__toc" data-bs-offset="120">

	<!--- Navbar --->
	<?php require_once('navbar.php'); ?>

	<!--- Main container --->
	<div class="w-full lg:flex">

		<!--- Left sidebar --->
		<?php if($showSidebar): ?>
		<aside class="border-r border-gray-900/10 dark:border-gray-50/[0.06] hidden lg:block lg:w-sidebar-lg 2xl:w-sidebar-2xl print:hidden">
			<div class="pt-10 px-6 sticky overflow-y-auto top-[theme(height.navbar)] h-[calc(100vh-theme(height.navbar)-1px)] lg:w-sidebar-lg 2xl:w-sidebar-2xl">
				<div class="dokuwiki-sidebar">
				<?php
				if($ACT != 'media') {
					tpl_includeFile('sidebarheader.html');
					_tpl_sidebar();
					tpl_includeFile('sidebarfooter.html');
				} else {
				?>
					<div class="panel namespaces">
						<h2>
							<?php echo hsc($lang['namespaces']) ?>
						</h2>
						<div class="panelHeader">
							<?php echo hsc($lang['media_namespaces']) ?>
						</div>

						<?php _tpl_mediaTree() ?>
					</div>
				<?php } ?>
				</div>
			</div>
		</aside>
		<?php endif ?>

		<!--- Middle content and left sidebar --->
		<main class="w-full lg:flex lg:flex-auto lg:overflow-x-hidden xl:[overflow-x:initial]">
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
					($toc ? 'ml-0 xl:ml-[theme(width.sidebar-lg)] 2xl:ml-[theme(width.sidebar-2xl)]' : ''),

					// print styles
					'print:w-full print:mx-0',
				) ?>">
					<?php html_msgarea(); ?>

					<?php require_once('content.php'); ?>
				</div>

				<!--- Right sidebar --->
				<div class="flex-none align-top hidden pl-8 text-sm xl:block xl:w-sidebar-lg 2xl:w-sidebar-2xl print:hidden">
					<div class="flex overflow-y-auto sticky top-[theme(height.navbar)] h-[calc(100vh-theme(height.navbar)-1px)] flex-col pt-10 pb-6">
						<?php
						if($toc) {
							echo '<h4 class="text-primary mb-4 pl-4 font-semibold">'
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
