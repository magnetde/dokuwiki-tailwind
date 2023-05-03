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
			'FORM_RECENT_OUTPUT'    => ['BEFORE', ['formRecentOutput']],
			'HTML_SECEDIT_BUTTON'   => ['AFTER',  ['htmlSecEditButton']],
			'TPL_CONTENT_DISPLAY'   => ['BEFORE', ['tplContent']],
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
				$value = $this->modifyRevision($value, false);
				$elm->val($value);
			}
		}
	}

	/**
	 * Modifies a single revision.
	 * If the revision contains a revision of the recent page,
	 * the second parameter must be true.
	 * Styles are applied with CSS.
	 */
	private function modifyRevision($content, $recent_page) {
		global $lang;

		if(strlen(trim($content)) == 0) {
			return $content;
		}

		$html = new simple_html_dom;
		$html->load($content, true, false);

		// ignore the pagenav element on the recent page
		if($recent_page && $html->find('div.pagenav', 0)) {
			$html->clear();
			unset($html);

			return $content;
		}

		// First collect the elements
		$date = $html->find('span.date', 0);
		$diff_link = $html->find('a.diff_link', 0); // may be null

		if($recent_page)
			$revisions_link = $html->find('a.revisions_link', 0);

		// revlink contains a description of the page
		$revlink = $html->find('a.wikilink1', 0);
		if(!$revlink) // revlink is either of class wikilink1 or wikilink2
			$revlink = $html->find('a.wikilink2', 0);

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
			.'<span class="subtitle">';

		// Add the page title to the subtitle
		if($recent_page) {
			$content .= '<span class="subtitle-name">' . $revlink->innertext . '</span>, ';
		}

		$content .= $date . ', ' .$user
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

		// Add revisions list
		if($recent_page) {
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">'
				.'<path fill="currentColor" d='
				.'"M4 17q-.425 0-.713-.288T3 16q0-.425.288-.713T4 15q.425 0 .713.288T5 16q0 .425-.288.713T4 17Zm0-4q-.425 0-.713-.288T3 12q0-.425.288-.713T4 11q.425 0 .713.288T5 12q0 .425-.288.713T4 13Zm0-4q-.425 0-.713-.288T3 8q0-.425.288-.713T4 7q.425 0 .713.288T5 8q0 .425-.288.713T4 9Zm3 8v-2h14v2H7Zm0-4v-2h14v2H7Zm0-4V7h14v2H7Z"'
				.'/></svg>';

			$revisions_link->innertext = $svg;
			$content .= $revisions_link->save();
		}

		// Add a button to the wikilink if it exists
		if(!$revlink->hasClass('wikilink2')) {
			$revlink->innertext = $lang['btn_preview'];
			$content .= $revlink->save();
		}

		$content .= '</div>';

		// Wrap around a diff element, so we can apply styles like padding for all children
		$content = '<div class="revision-info">' . $content . '</div>';

		// Add a gray dot to the vertical line
		if($recent_page) {
			$content = '<div class="'
				.clsx("
					absolute w-3 h-3 rounded-full mt-5 -left-1.5
					bg-gray-200 dark:bg-gray-700
					ring-4 ring-white dark:ring-gray-900
				")
				.'"></div>'
				.$content;
		}

		$html->clear();
		unset($html);

		return $content;
	}

	public function formRecentOutput(\Doku_Event $event) {
		$form = $event->data;

		for($i = 0; $i < $form->elementCount(); $i++) {
			$elm  = $form->getElementAt($i);
			$type = $elm->getType();

			if($type == 'html') {
				$value = $elm->val();
				$value = $this->modifyRevision($value, true);
				$elm->val($value);
			}
		}
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
		if(strlen($content) > MAX_FILE_SIZE)
			return $content;

		// Import HTML string
		$html = new simple_html_dom;
		$html->load($content, true, false);

		// Return original content if Simple HTML DOM fail or exceeded page size (default MAX_FILE_SIZE => 600KB)
		if(!$html)
			return $content;

		$this->modifyHeaders($html);
		$this->modifyDownloadBlocks($html);
		$this->modifyEditor($html);
		$this->modifySearch($html);

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

	/**
	 * Modifies the search page by adding a tab bar and moving the results into tabbed containers.
	 */
	private function modifySearch($html) {
		$form = $html->find('.search-form', 0);
		if(!$form) // check, if the current content shows search results
			return;

		// determine the number of elements per tab and the tab contents
		list($results1, $count1, $results2, $count2) = $this->getSearchTabs($html);

		$show_tab1 = $count1 > 0 || $count2 == 0;
		$tabnav = '<div class="search-box">';

		// create the tab header
		$tabnav .= '<div class="search-header">'
			.'<button type="button" role="tab" id="tab-quickhits" class="search-tab'
			.($show_tab1 ? ' active' : '') . '">'
			.tpl_getLang('search_title')
			.'<span class="count">' . $count1 . '</span>'
			.'</button>'
			.'<button type="button" role="tab" id="tab-fulltext" class="search-tab'
			.(!$show_tab1 ? ' active' : '') . '">'
			.tpl_getLang('search_content')
			.'<span class="count">' . $count2 . '</span>'
			.'</button>'
			.'</div>';

		// add the tab content
		$tabnav .= $results1;
		$tabnav .= $results2;
		$tabnav .= '</div>';

		// Set the html element
		$form->outertext .= $tabnav;
	}

	/**
	 * Determines the content and the number of results for both tabs.
	 */
	private function getSearchTabs($html) {
		$nothing = $html->find('.nothing', 0);

		if(empty($nothing)) {
			// determine element and count of tab 1
			list($elm1, $count1) = $this->getResultsElement($html,
				'search_quickresult', 'tab-content-quickhits', '.search_quickhits li');

			// determine element and count of tab 2
			list($elm2, $count2) = $this->getResultsElement($html,
				'search_fulltextresult', 'tab-content-fulltext', '.search_results .search_fullpage_result');

			// add the active class of the active tab
			$show_tab1 = $count1 > 0 || $count2 == 0;
			$results1 = $this->resultSetActive($elm1, true, $show_tab1);
			$results2 = $this->resultSetActive($elm2, false, !$show_tab1);
		} else {
			// remove the nothing element
			$nothing->outertext = '';

			list($results1, $count1) = array($this->resultsEmpty(true, true), 0);
			list($results2, $count2) = array($this->resultsEmpty(false, false), 0);
		}

		return array($results1, $count1, $results2, $count2);
	}

	/**
	 * Determines the element, that shows the results by adding an id, by removing the header
	 * and by determining the number of search results.
	 */
	private function getResultsElement($html, $class, $id, $resultSelector) {
		$elm = $html->find('.'.$class, 0);
		if(!$elm)
			return array(null, 0);

		$elm->setAttribute('id', $id);
		$elm->find('h2', 0)->outertext = '';

		$count = count($elm->find($resultSelector));

		return array($elm, $count);
	}

	/**
	 * Adds the active class of the result element, if the $active parameter is true.
	 * If the element $elm is not null, the active class is added to the element if needed,
	 * the html string is determined and the html text of the element is set to the empty string.
	 * If the element is null, an empty result page is returned.
	 */
	private function resultSetActive($elm, $quickhit, $active) {
		if($elm) {
			if($active)
				$elm->addClass('active');

			$results = $elm->save();
			$elm->outertext = '';
		} else
			$results = $this->resultsEmpty($quickhit, $active);
		
		return $results;
	}

	/**
	 * Creates a empty result tab.
	 */
	private function resultsEmpty($quickhit, $active) {
		global $lang;

		if($quickhit) {
			$id = 'tab-content-quickhits';
			$class = 'search_quickresult';
		} else {
			$id = 'tab-content-fulltext';
			$class = 'search_fulltextresult';
		}

		return '<div id="' . $id . '" class="' . $class
			.($active ? ' active' : '') . '">'
			.'<div class="nothing">' . $lang['nothingfound']  . '</div>'
			.'</div>';
	}
}
