<?php

/**
 * Creates a tooltip element for a given element id and a label.
 */
function createTooltip($id, $label) {
	$tooltip = '<div id="' . $id . '" role="tooltip" class="tooltip-container">' . $label
		.'<div class="tooltip-arrow" data-popper-arrow></div>'
		.'</div>';

	return $tooltip;
}
