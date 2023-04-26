<div class="flex-auto max-w-6xl min-w-0 pt-6 lg:px-8 lg:pt-8">

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

	<div class="flex flex-col justify-between mt-16 min-h-[calc(100%-6rem-2px)]">

		<!--- Content --->
		<article id="dw-content" class="<?php echo clsx("
			dw-content
			w-full max-w-none
			prose dark:prose-invert
			prose-headings:scroll-mt-20
			prose-pre:rounded-lg
			prose-ul:my-2 prose-li:my-1
		") ?>">

			<?php echo $content ?>

		</article>

		<!--- Footer --->
		<footer class="mt-10 text-sm">
			<hr class="h-px my-6 bg-gray-200 border-0 dark:bg-gray-700">

			<div class="my-6 text-center text-gray-500 dark:border-gray-200/5">
				<span class="block"><?php tpl_pageinfo() ?></span>
				<span class="block mt-7"><?php tpl_license('0') ?></span>
			</div>
		</footer>

	</div>
</div>
