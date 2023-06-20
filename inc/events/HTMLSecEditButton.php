<?php

use simple_html_dom\simple_html_dom;

/**
 * Event handler that modifies the section edit buttons by adding tooltips.
 */
class HTMLSecEditButton extends EventHandler {

	protected function event() {
		return 'HTML_SECEDIT_BUTTON';
	}

	protected function advise() {
		return 'AFTER';
	}

	public function handle(\Doku_Event $event) {
		global $lang;

		$html = new simple_html_dom;
		$html->load($event->result, true, false);

		// Iterate over all edit button forms
		foreach($html->find('.btn_secedit') as $elm) {
			// Add the icon to the button
			$btn = $elm->find('button', 0);
			$btn->innertext = '';

			if(!$lang['media_edit'])
				continue;

			// determine the id of the edit button
			$elm_id = $elm->find('input[name="hid"]', 0);
			if(!$elm_id)
				continue;

			$id = 'edit-button-' . $elm_id->value;

			// Add a new tooltip element

			$btn->setAttribute('data-tooltip-target', $id);
			$btn->setAttribute('data-tooltip-placement', 'bottom');

			$label = sprintf($lang['media_edit'], $lang['doublequoteopening'] . $btn->title . $lang['doublequoteclosing']);

			// Add the tooltip element
			$elm->find('.no', 0)->innertext .= createTooltip($id, $label);
		}

		$event->result = $html->save();

		$html->clear();
		unset($html);
	}
}
