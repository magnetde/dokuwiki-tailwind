<?php

use simple_html_dom\simple_html_dom;

/**
 * Event handler that modifies the section edit buttons.
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

		$edit_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">'
		.'<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />'
		.'</svg>';

		// Iterate over all edit button forms
		foreach($html->find('.btn_secedit') as $elm) {
			// Add the icon to the button
			$btn = $elm->find('button', 0);
			$btn->innertext = $edit_icon;

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
