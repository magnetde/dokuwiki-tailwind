<?php

use simple_html_dom\simple_html_dom;

/**
 * Class, that handles all DokuWiki events.
 */
class EventHandlers {

	public function __construct() {
		global $EVENT_HANDLER;

		# Event => [ ADVISE, METHOD ]
		$events_dispatcher = [
			'FORM_REVISIONS_OUTPUT' => ['BEFORE', ['formRevisionsOutput']],
			'HTML_SECEDIT_BUTTON' => ['AFTER',  ['htmlSecEditButton']],
			'TPL_CONTENT_DISPLAY' => ['BEFORE', ['tplContent']],
		];

		foreach($events_dispatcher as $event => $data) {
			list($advise, $methods) = $data;

			foreach($methods as $method) {
				$EVENT_HANDLER->register_hook($event, $advise, $this, $method);
			}
		}
	}

	public static function initialize() {
		static $instance = null;

		if($instance === null) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Modifies the revisions list but does not apply any styles.
	 * Styles are applied with CSS.
	 */
	public function formRevisionsOutput(\Doku_Event $event) {
		$form = $event->data;

		for($i = 0; $i < $form->elementCount(); $i++) {
			$elm  = $form->getElementAt($i);
			$type = $elm->getType();

			if($type == 'html') {
				$value = $elm->val();
				$value = $this->modifyRevision($value);
				$elm->val($value);
			}
		}
	}

	/**
	 * Modifies a single revision.
	 * Styles are applied with CSS.
	 */
	private function modifyRevision($content) {
		global $lang;

		if(strlen(trim($content)) == 0) {
			return $content;
		}

		$html = new simple_html_dom;
		$html->load($content, true, false);

		// First collect the elements
		$date = $html->find('span.date', 0);
		$diff_link = $html->find('a.diff_link', 0); // may be null
		$revlink = $html->find('a.wikilink1', 0);
		$summary = $html->find('span.sum', 0);
		$user = $html->find('span.user', 0);
		$sizechange = $html->find('span.sizechange', 0);

		// Modify the summary text, if it is empty
		$summary_text = _tpl_remove_prefix($summary->innertext, ' – ');
		if(empty($summary_text)) {
			$summary_text = tpl_getLang('no_description');
			$summary->addClass('empty');
		}

		$summary->innertext = $summary_text;

		// Then reorder them
		$content = '';

		// Add the left content
		$content .= '<div class="rev-description">' // the left content
			.'<div class="summary">' // first line (description and size change)
			.$summary->save()
			.$sizechange->outertext
			.'</div>'
			.'<span class="date-user">'
			.$date . ', ' .$user
			.'</span>'
			.'</div>';

		// Add the right content
		$content .= '<div class="revision-buttons" role="group">';

		// Modify the diff link, if exists
		if($diff_link) {
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">'
				.'<path fill="currentColor" d="'
				.'m15.3 13.3l-3.6-3.6q-.15-.15-.212-.325T11.425 9q0-.2.063-.375T11.7 8.3l3.6-3.6q.3-.3.7-.3t.7.3q.3.3.3.713t-.3.712L14.825 8H21q.425 0 .713.288T22 9q0 .425-.288.713T21 10h-6.175l1.875 1.875q.3.3.3.7t-.3.7q-.3.3-.687.325t-.713-.3Zm-8 5.975q.3.3.7.313t.7-.288l3.6-3.6q.15-.15.212-.325t.063-.375q0-.2-.063-.375T12.3 14.3l-3.6-3.6q-.3-.3-.7-.3t-.7.3q-.3.3-.3.713t.3.712L9.175 14H3q-.425 0-.713.288T2 15q0 .425.288.713T3 16h6.175L7.3 17.875q-.3.3-.3.7t.3.7Z'
				.'"/></svg>';

			$diff_link->innertext = $svg;
			$content .= $diff_link->save();
		}

		// Modify the revision link
		$revlink->innertext = $lang['btn_preview'];
		$content .= $revlink->save();

		$content .= '</div>';

		// Wrap around a diff element, so we can apply styles like padding for all children
		$content = '<div class="revision-info">' . $content . '</div>';

		$html->clear();
		unset($html);

		return $content;
	}

	public function htmlSecEditButton(\Doku_Event $event) {
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
			$elm->find('.no', 0)->innertext .= $this->createTooltip($id, $label);
		}

		$event->result = $html->save();
		$html->clear();
		unset($html);
	}

	/**
	 * Creates a tooltip element for a given element id and a label.
	 */
	private function createTooltip($id, $label) {
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

	public function tplContent(\Doku_Event $event) {
		$event->data = $this->modifyContent($event->data);
	}

	private function modifyContent($content) {
		// FIX :-\ smile
		$content = str_replace(['alt=":-\"', "alt=':-\'"], 'alt=":-&#92;"', $content);

		// Return original content if Simple HTML DOM fail or exceeded page size (default MAX_FILE_SIZE => 600KB)
		if(strlen($content) > MAX_FILE_SIZE) {
			return $content;
		}

		// Import HTML string
		$html = new simple_html_dom;
		$html->load($content, true, false);

		// Return original content if Simple HTML DOM fail or exceeded page size (default MAX_FILE_SIZE => 600KB)
		if(!$html) {
			return $content;
		}

		$this->modifyHeaders($html);
		$this->modifyDownloadBlocks($html);
		$this->modifyEditor($html);

		$content = $html->save();
		$html->clear();
		unset($html);

		return $content;
	}

	// Add anchors to headings
	private function modifyHeaders($html) {
		$headers = array('h1', 'h2', 'h3', 'h4'); // no anchor for h5

		foreach($headers as $header) {
			foreach($html->find($header) as $elm) {
				$elm->addClass('group');

				$class = clsx("
					ml-3 no-underline opacity-0 transition-opacity group-hover:opacity-100
					text-gray-300 hover:text-gray-400
					dark:text-gray-500 dark:hover:text-gray-400
				");

				$elm->innertext .= '<a class="' . $class . '" href="#' . $elm->id . '">#</a>';
			}
		}
	}

	/**
	 * Modifies downloadable code blocks, by adding a bar at the top with an download button.
	 */
	private function modifyDownloadBlocks($html) {
		foreach($html->find('dl.code dt a, dl.file dt a') as $elm) {
			$path = $elm->innertext;
			$href = $elm->href;
			$title = $elm->title;

			$id = bin2hex($path);

			$elm->outertext = '<span class="'
				.clsx("
					font-semibold overflow-hidden whitespace-nowrap text-ellipsis text-gray-700 dark:text-gray-300
				")
				.'">' . $path . '</span>'
				.'<a href="' . $href . '" class="'
				.clsx("
					flex items-center p-2 text-xs font-medium text-gray-700
					bg-white border border-gray-200 rounded-lg hover:bg-gray-100
					hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-gray-300
					dark:focus:ring-gray-500 dark:bg-gray-800 focus:outline-none dark:text-gray-400
					dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700
				")
				.'" data-tooltip-target="' . $id . '" data-tooltip-placement="bottom">'
				.'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">'
				.'<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />'
				.'</svg>'
				.'</a>'
				.$this->createTooltip($id, $title);
		}
	}

	/**
	 * Modifies the editor by changing the draft status.
	 * The old draft status gets hidden with CSS because otherwise the draft service may not work correctly.
	 */
	private function modifyEditor($html) {
		$elm = $html->find('.editBox #tool__bar', 0);
		if(!$elm)
			return;

		$tooltip_id = 'draft-status-tooltip';
		$tooltip_text_id = 'draft-status-text';

		// Simply append a new child to the parent by append to the outertext
		$elm->outertext .= '<div id="draft-icon" class="draft-icon" data-tooltip-placement="bottom" '
			.'data-tooltip-target="' . $tooltip_id
			.'"></div>'

			// span needed, so the tooltip can be dynamically changed
			.$this->createTooltip($tooltip_id, '<span id="'.$tooltip_text_id.'"></span>');
	}
}
