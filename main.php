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

$showTools = !tpl_getConf('hideTools') || (tpl_getConf('hideTools') && !empty($_SERVER['REMOTE_USER']));
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
		<header class="<?php echo clsx("
			sticky top-0 z-50 flex-none w-full mx-auto
			backdrop-blur transition-colors duration-500
			bg-white/95, border-b border-gray-900/10 dark:bg-transparent
			dark:border-gray-50/[0.06] supports-backdrop-blur:bg-white/60
		") ?>">
			<div class="<?php echo clsx("
				flex items-center justify-between w-full
				px-4 py-4 mx-auto max-w-10xl lg:px-4
			") ?>">
				<!--- Logo and title --->
				<?php
				$home_link = (tpl_getConf('homePageURL') ? tpl_getConf('homePageURL') : wl());
				$title = $conf['title'];

				echo '<a class="flex items-center" href="' . $home_link . '" accesskey="h" title="' . $title . '">';

				if(tpl_getConf('showIcon')) {
					$logo_url = tpl_basedir() . 'images/logo.png';
					echo '<img class="h-8 mr-3" src="' . $logo_url . '" alt="' . $title . '"/>';
				}

				echo '<span class="self-center text-2xl font-semibold whitespace-nowrap overflow-ellipsis dark:text-white">' . $title . '</span>';
				echo '</a>';
				?>

				<div class="flex items-center md:order-1">
					<?php echo _tpl_searchform() ?>
				</div>

				<!--- Mobile menu --->
				<button class="<?php echo clsx("
					inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg md:hidden order-3
					hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200
					dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600
				") ?>" type="button" data-collapse-toggle="navbar-default" aria-controls="navbar-default" aria-expanded="false">
					<span class="sr-only">
						<?php tpl_getLang('open_menu') ?>
					</span>
					<svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
					</svg>
				</button>

				<!--- Tools dropdown and user avatar --->
				<div class="hidden items-center md:order-2 md:flex" id="navbar-default">
					<ul class="<?php echo clsx("
						flex flex-col font-medium p-4 md:p-0 mt-4 items-center
						border border-gray-100 rounded-lg bg-gray-50
						md:flex-row md:space-x-8 md:mt-0 md:border-0
						md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700
					") ?>">
						<li>
							<!--- Tools button --->
							<button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar" class="<?php echo clsx("
								flex items-center justify-between w-full py-2 pl-3 pr-4 text-gray-900 rounded
								hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto
								dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent
							") ?>">
								<?php echo $lang['tools'] ?> <svg class="w-5 h-5 ml-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
								</svg>
							</button>

							<!-- Dropdown menu of the tools -->
							<div id="dropdownNavbar" class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
								<ul class="py-2 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
									<?php
									$menu_items = (new \dokuwiki\Menu\SiteMenu())->getItems();
									foreach($menu_items as $item) {
										echo '<li>'
											.'<a class="'
											.clsx("
												block px-4 py-2 hover:bg-gray-100
												dark:hover:bg-gray-600 dark:hover:text-white
											")
											.'" href="'.$item->getLink().'" title="'.$item->getTitle().'">'
											.$item->getLabel()
											.'</a></li>';
									}
									?>
								</ul>
							</div>
						</li>
						<li>
							<!--- Avatar button --->
							<button type="button" class="<?php echo clsx("
								flex mr-3 first-line:text-sm rounded-full md:mr-0
								bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600
							") ?>" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
								<span class="sr-only">
									<?php tpl_getLang('open_menu') ?>
								</span>

								<?php
								$user = $_SERVER['REMOTE_USER'];

								if(!empty($user)) {
									$avatar_url = _tpl_getavatar($user, $INFO['userinfo']['mail'], $avatar_size);
									echo '<img class="w-8 h-8 rounded-full" src="' . $avatar_url . '" alt="' . $user . '">';
								} else {
									echo '<div class="relative w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">'
										.'<svg class="absolute w-12 h-12 text-gray-400 -left-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">'
										.'<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">'
										.'</path></svg>';
								}
								?>
							</button>

							<!-- Dropdown menu of the avatar -->
							<div class="z-60 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
								<?php
								if(!empty($_SERVER['REMOTE_USER'])) {
									echo '<div class="px-4 py-3">'
										.'<span class="block text-sm text-gray-900 dark:text-white">' . hsc($INFO['userinfo']['name']) . '</span>'
										.'<span class="block text-sm  text-gray-500 truncate dark:text-gray-400">' . $INFO['userinfo']['mail'] . '</span>'
										.'</div>';
								}
								?>
								<ul class="py-2" aria-labelledby="user-menu-button">
									<?php
									$menu_items = (new \dokuwiki\Menu\UserMenu())->getItems();
									foreach($menu_items as $item) {
										echo '<li><a class="'
											.clsx("
												block px-4 py-2
												text-sm text-gray-700 hover:bg-gray-100
												dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white
											")
											.'" href="' . $item->getLink() . '">' . $item->getLabel() . '</a></li>';
									}
									?>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</header>

		<?php
		// Render the content initially
		ob_start();
		tpl_content(false);
		$buffer = ob_get_clean();
		?>

		<!--- Main container --->
		<div class="w-full z-10 px-4 mx-auto max-w-10xl">
			<div class="lg:flex">
				<!--- Left sidebar --->
				<aside class="fixed inset-0 z-20 flex-none hidden h-full w-72 lg:static lg:h-auto lg:overflow-y-visible lg:pt-0 lg:w-64 lg:block">
					<!--- TODO --->
					left (mt-4 needed for first item)
				</aside>

				<!--- Middle content and left sidebar --->
				<main class="flex-auto w-full min-w-0 lg:static lg:max-h-full lg:overflow-visible">
					<div class="flex w-full">
						<!--- Main content --->
						<div class="flex-auto max-w-6xl min-w-0 pt-6 lg:px-8 lg:pt-8 pb:12 xl:pb-24 lg:pb-16">

							<!--- Breadcrumbs and page tool buttons --->
							<div class="flex flex-nowrap items-center justify-between">
								<!--- Navigation and breadcrumbs --->
								<?php
								if($conf['breadcrumbs'] || $conf['youarehere']) {
									// only display one of both, else it gets ugly and I want a clean UI

									$youarehere = !empty($conf['youarehere']);

									echo '<nav aria-label="breadcrumb" role="navigation" class="breadcrumb-list';
									if(!$youarehere) echo ' fade'; // only fade, when showing last opened pages
									echo '">';
									echo _tpl_breadcrumbs($youarehere);
									echo '</nav>';
								}
								?>

								<!--- Buttons --->
								<div class="flex items-center justify-end space-x-2">
									<?php
									$index = 0;
									$menu_items = (new \dokuwiki\Menu\PageMenu())->getItems();
									foreach($menu_items as $item) {
										if ($item->getType() == "top") continue; // ignore the top button because the button are already at the top

										// Button
										echo '<a href="' . $item->getLink()  . '" data-tooltip-target="pagetool-button-' . $index . '" class="'
											.clsx("
												page-tool-btn
												flex items-center p-2 text-xs font-medium text-gray-700
												bg-white border border-gray-200 rounded-lg
												hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-gray-300
												dark:focus:ring-gray-500 dark:bg-gray-800 focus:outline-none dark:text-gray-400
												dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700
											")
											.'" data-tooltip-placement="bottom">'
											.inlineSVG($item->getSvg())
											.'</a>';

										// Tooltip
										echo '<div id="pagetool-button-' . $index . '" role="tooltip" class="'
											.clsx("
												absolute z-10 inline-block px-3 py-2
												text-sm font-medium text-white
												transition-opacity duration-300
												bg-gray-900 rounded-lg shadow-sm tooltip
												dark:bg-gray-700 opacity-0 invisible
											")
											.'">'
											.$item->getLabel()
											.'<div class="tooltip-arrow" data-popper-arrow=""></div>'
											.'</div>';

										$index++;
									}
									?>
								</div>
							</div>

							<div class="h-full flex flex-col flex-wrap justify-between mt-16">

								<!--- Main content --->
								<article id="dw-content" class="<?php echo clsx("
									prose dark:prose-invert w-full max-w-none
									prose-headings:scroll-mt-24
									prose-pre:rounded-lg
								") ?>">
									<?php echo $buffer ?>
								</article>

								<!--- Footer --->
								<div class="">
									Footer
								</div>

							</div>
						</div>

						<!--- Right sidebar --->
						<div class="flex-none hidden w-64 pl-8 mr-8 xl:text-sm xl:block">
							<!--- TODO --->
							right
						</div>
					</div>
				</main>
			</div>
		</div>
	</body>
</html>
