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
			'HTML_SECEDIT_BUTTON'           => ['AFTER',  ['htmlSecEditButton']],
			'TPL_CONTENT_DISPLAY'           => ['BEFORE', ['tplContent']],
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
}
