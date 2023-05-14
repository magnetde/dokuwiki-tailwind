<header class="<?php echo clsx("
	sticky h-navbar top-0 z-50 flex-none w-full
	backdrop-blur transition-colors duration-500
	bg-white/95, border-b border-gray-900/10 dark:bg-transparent
	dark:border-gray-50/[0.06] supports-backdrop-blur:bg-white/60
") ?>">
	<div class="<?php echo clsx("
		flex items-center justify-between
		md:grid md:grid-cols-3
		w-full h-full px-2 lg:px-4
	") ?>">
		<!--- Logo and title --->
		<?php
		$home_link = (tpl_getConf('homePageURL') ? tpl_getConf('homePageURL') : wl());
		$title = $conf['title'];

		echo '<a class="flex items-center w-fit" href="' . $home_link . '" accesskey="h" title="' . $title . '">';

		if(tpl_getConf('showIcon')) {
			$logo_url = tpl_basedir() . 'images/logo.png';
			echo '<img class="h-8 mr-3" src="' . $logo_url . '" alt="' . $title . '"/>';
		}

		echo '<span class="self-center text-2xl font-semibold truncate dark:text-white">' . $title . '</span>';
		echo '</a>';
		?>

		<!--- Search field --->
		<div class="flex items-center md:order-1 md:justify-center">
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
		<div class="hidden items-center md:order-2 md:flex md:justify-end" id="navbar-default">
			<ul class="<?php echo clsx("
				flex flex-col font-medium p-4 mt-4 items-center border rounded-lg
				md:p-0 md:flex-row md:space-x-8 md:mt-0 md:border-0
				border-gray-100 bg-gray-50
				dark:bg-gray-800 dark:border-gray-700
				md:bg-transparent
			") ?>">
				<li>
					<!--- Tools button --->
					<button data-dropdown-toggle="dropdown-tools" class="<?php echo clsx("
						md:btn-link
						flex items-center justify-between w-full
					") ?>">
						<?php echo $lang['tools'] ?> <svg class="w-5 h-5 ml-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
						</svg>
					</button>

					<!-- Dropdown menu of the tools -->
					<div id="dropdown-tools" class="dropdown-container w-44">
						<ul class="py-2 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
							<?php
							$menu_items = (new \dokuwiki\Menu\SiteMenu())->getItems();
							foreach($menu_items as $item) {
								echo '<li>'
									.'<a class="dropdown-element" href="'.$item->getLink().'" title="'.$item->getTitle().'">'
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
						flex mr-3 first-line:text-sm rounded-full md:mr-0 focus:ring-4
						bg-gray-800 focus:ring-gray-300 dark:focus:ring-gray-600
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
					<div id="user-dropdown" class="dropdown-container my-4">
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
								echo '<li><a class="dropdown-element" href="' . $item->getLink() . '">' . $item->getLabel() . '</a></li>';
							}
							?>
						</ul>
					</div>
				</li>
			</ul>
		</div>
	</div>
</header>
