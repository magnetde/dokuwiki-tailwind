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
@require_once dirname(__FILE__) . '/inc/clsx.php'; /* Include helper function for joining classes */

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

		<!-- CSS Files -->
		<link href="<?php echo tpl_basedir(); ?>assets/css/main.css" rel="stylesheet" />
		<!-- JS -->
		<script src="<?php echo tpl_basedir(); ?>assets/js/flowbite.min.js" type="text/javascript"></script>
	</head>

	<body class="dark:bg-gray-900 antialiased">

		<!--- Navbar --->
		<header class="<?php echo clsx("
			sticky top-0 z-50 flex-none w-full mx-auto
			backdrop-blur transition-colors duration-500
			bg-white/95, border-b border-slate-900/10 dark:bg-transparent
			dark:border-slate-50/[0.06] supports-backdrop-blur:bg-white/60
		") ?>">
			<div class="<?php echo clsx("
				flex items-center justify-between w-full
				px-4 py-4 mx-auto max-w-8xl lg:px-4
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

				<!--- Mobile menu --->
				<button class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg md:hidden order-3 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" type="button" data-collapse-toggle="navbar-default" aria-controls="navbar-default" aria-expanded="false">
					<span class="sr-only">
						<?php tpl_getLang('open_menu') ?>
					</span>
					<svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
					</svg>
				</button>

				<!--- User avatar --->
				<div class="hidden items-center md:order-2 md:flex">
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

					<!-- Dropdown menu -->
					<div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
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
				</div>

				<div class="flex items-center md:order-1">
					<?php echo _tpl_searchform() ?>
				</div>
			</div>
		</header>

		<!--- Main container --->
		<div>
			<div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">

				<!--- Left sidebar --->
				<div class="hidden lg:block fixed z-20 left-[max(0px,calc(50%-45rem))] right-auto w-[19.5rem] py-10 px-8 overflow-y-auto">
					<!--- TODO --->
					left
				</div>

				<!--- Middle content and left sidebar --->
				<div class="lg:pl-[19.5rem]">
					<div class="max-w-3xl mx-auto pt-10 xl:max-w-none xl:ml-0 xl:mr-[15.5rem] xl:pr-16">
						<!--- Main content --->
						<!--- TODO --->
						mid

						<!--- Left sidebar --->
						<div class="fixed z-20 top-[4.5rem] bottom-0 right-[max(0px,calc(50%-45rem))] w-[19.5rem] py-10 overflow-y-auto hidden xl:block">
							<div class="px-8">
								<!--- TODO --->
								right
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
