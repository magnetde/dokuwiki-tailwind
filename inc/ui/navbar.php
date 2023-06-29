<header class="<?php echo clsx("
	sticky h-navbar top-0 z-50 flex-none w-full
	backdrop-blur border-b transition-colors duration-500
	border-gray-900/10 bg-white/95 supports-backdrop-blur:bg-white/70
	dark:border-gray-50/[0.06] dark:bg-transparent
	print:bg-white print:border-b-2
") ?>">
	<div class="<?php echo clsx("
		flex items-center justify-between
		md:grid md:grid-cols-3
		w-full h-full px-2 lg:px-4
		print:px-2
	") ?>">
		<!--- Logo and title --->
		<?php
		$home_link = (tpl_getConf('homePageURL') ? tpl_getConf('homePageURL') : wl());
		$title = $conf['title'];

		echo '<a class="inline-block md:flex items-center mr-3 print:max-w-full" href="' . $home_link . '" accesskey="h" title="' . $title . '">';

		if(tpl_getConf('showIcon')) {
			$logo_url = tpl_basedir() . 'images/logo.png';
			echo '<img class="w-8 h-8 max-w-none md:mr-3" src="' . $logo_url . '" alt="' . $title . '"/>';
		}

		echo '<span class="hidden self-center text-2xl font-semibold truncate text-gray-900 dark:text-white md:inline">' . $title . '</span>';
		echo '</a>';
		?>

		<!--- Search field --->
		<div class="flex items-center print:hidden">
			<?php _tpl_searchform() ?>
		</div>

		<!--- Tools dropdown and user avatar --->
		<div class="hidden items-center md:flex md:justify-end print:hidden">
			<ul class="<?php echo clsx("
				flex flex-row items-center rounded-lg space-x-8
			") ?>">
				<!--- Tools button --->
				<li>
					<button data-dropdown-toggle="dropdown-tools" class="<?php echo clsx("
						btn-link flex items-center justify-between w-full
					") ?>">
						<?php echo $lang['tools'] ?>
						<svg class="w-5 h-5 ml-1 fill-gray-400 dark:fill-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
						</svg>
					</button>

					<!-- Dropdown menu of the tools -->
					<div id="dropdown-tools" class="dropdown-container w-44">
						<ul class="py-2" aria-labelledby="dropdownLargeButton">
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

				<!--- Avatar button --->
				<li>
					<button type="button" class="<?php echo clsx("
						flex mr-3 first-line:text-sm rounded-full md:mr-0 bg-gray-800
					") ?>" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
						<span class="sr-only">
							<?php tpl_getLang('open_menu') ?>
						</span>

						<?php _tpl_avatar() ?>
					</button>

					<!-- Dropdown menu of the avatar -->
					<div id="user-dropdown" class="dropdown-container my-4">
						<?php
						if(!empty($_SERVER['REMOTE_USER']))
							_tpl_userInfo();
						?>
						<ul class="py-2" aria-labelledby="user-menu-button">
							<?php
							$menu_items = (new \dokuwiki\Menu\UserMenu())->getItems();
							foreach($menu_items as $item)
								echo '<li><a class="dropdown-element" href="' . $item->getLink() . '">' . $item->getLabel() . '</a></li>';
							?>
						</ul>
					</div>
				</li>
			</ul>
		</div>

		<!--- Mobile menu button --->
		<button class="<?php echo clsx("
			text-secondary
			inline-flex items-center p-2 ml-1 text-sm rounded-lg md:hidden focus:ring-2
			hover:bg-gray-100 focus:outline-none focus:ring-gray-200
			dark:hover:bg-gray-700 dark:focus:ring-gray-600
			print:hidden
		") ?>" type="button" data-collapse-toggle="navbar-mobile-menu" aria-controls="navbar-mobile-menu" aria-expanded="false">
			<span class="sr-only">
				<?php tpl_getLang('open_menu') ?>
			</span>
			<svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
			</svg>
		</button>
	</div>
</header>

<!--- Mobile menu --->
<!--- The mobile menu is added as an sibling element to the navbar because it needs a lower z-index to fix some weird overscroll animations --->
<div class="hidden fixed z-40 top-0 left-0 w-screen h-screen overflow-y-auto bg-gray-900/50 dark:bg-gray-900/80 print:hidden" id="navbar-mobile-menu">
	<div class="<?php echo clsx("
		flex flex-col justify-stretch w-full px-2 pt-[theme(height.navbar)] border-b
		divide-y divide-gray-200 dark:divide-gray-700
		border-gray-900/10 bg-white
		dark:border-gray-50/5 dark:bg-gray-900
	") ?>">
		<!--- User info and user actions --->
		<?php if(!empty($_SERVER['REMOTE_USER'])) { ?>
		<div class="flex flex-col justify-stretch pt-4">
			<div class="flex items-center px-3">
				<?php _tpl_avatar() ?>
				<?php _tpl_userInfo() ?>
			</div>
			<ul class="pb-2">
				<?php
				$menu_items = (new \dokuwiki\Menu\UserMenu())->getItems();
				foreach($menu_items as $item)
					echo '<li><a class="nav-item" href="' . $item->getLink() . '">' . $item->getLabel() . '</a></li>';
				?>
			</ul>
		</div>
		<?php } ?>

		<!--- Tools --->
		<ul class="py-2">
			<div class="pt-3 px-3 mb-2 text-sm font-semibold text-gray-500">
				<?php echo $lang['tools'] ?>
			</div>

			<?php
			$menu_items = (new \dokuwiki\Menu\SiteMenu())->getItems();
			foreach($menu_items as $item)
				echo '<li><a class="nav-item" href="' . $item->getLink() . '">' . $item->getLabel() . '</a></li>';
			?>
		</ul>

		<!--- Sidebar --->
		<?php
		$sidebarInNavbar = $ACT == 'show' && page_findnearest($conf['sidebar']);

		if($sidebarInNavbar) { ?>
		<div class="dokuwiki-sidebar pt-5 px-3">
			<?php _tpl_sidebar() ?>
		</div>
		<?php } ?>
	</div>
</div>
