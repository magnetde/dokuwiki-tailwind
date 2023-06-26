<div class="<?php echo clsx("
	flex flex-col justify-between w-full min-h-[calc(100vh-theme(height.navbar)-1px)]
	px-4 md:px-6 lg:px-8 pt-6 lg:pt-8
	print:block print:px-2
") ?>">

	<div class="flex flex-col print:block">

		<!--- Breadcrumbs and page tool buttons --->
		<div class="flex flex-nowrap items-center justify-between print:hidden">
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

					$attrs = $item->getLinkAttributes();

					// Button
					echo '<a href="' . $attrs['href']  . '" data-tooltip-target="pagetool-button-' . $index . '" '
						.'class="' . clsx('btn-icon page-tool-btn', $attrs['class']) . '" data-tooltip-placement="bottom">'
						.inlineSVG($item->getSvg())
						.'</a>';

					// Tooltip
					echo '<div id="pagetool-button-' . $index . '" role="tooltip" class="tooltip-container">'
						.$item->getLabel()
						.'<div class="tooltip-arrow" data-popper-arrow=""></div>'
						.'</div>';

					$index++;
				}
				?>
			</div>
		</div>

		<!--- Content --->
		<article id="dw-content" class="<?php echo clsx(
			"dokuwiki mt-16 print:mt-6",
			_tpl_page_classes(),
		) ?>">

			<?php echo $content ?>

		</article>
	</div>

	<!--- Footer --->
	<footer class="mt-10 text-sm">
		<hr class="h-px my-6 border-0 bg-gray-200 dark:bg-gray-700">

		<div class="px-4 my-6 text-center text-secondary">
			<span class="block"><?php tpl_pageinfo() ?></span>
			<span class="block mt-7"><?php tpl_license('0') ?></span>
		</div>
	</footer>

</div>
