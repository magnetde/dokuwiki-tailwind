<?php

/**
 * Creates a tooltip element for a given element id and a label.
 */
function createTooltip($id, $label) {
	$tooltip = '<div id="' . $id . '" role="tooltip" class="'
		.clsx("
			absolute z-10 inline-block px-3 py-2
			text-sm font-medium text-white text-center
			transition-opacity duration-300
			bg-gray-900 rounded-lg shadow-sm tooltip
			dark:bg-gray-700 opacity-0 invisible
		")
		.'">' . $label
		.'<div class="tooltip-arrow" data-popper-arrow></div>'
		.'</div>';

	return $tooltip;
}
