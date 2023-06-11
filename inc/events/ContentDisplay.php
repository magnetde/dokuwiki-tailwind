<?php

use simple_html_dom\simple_html_dom;

/**
 * Event handler that modifies the content.
 */
class TPLContentDisplay extends EventHandler {

	protected function event() {
		return 'TPL_CONTENT_DISPLAY';
	}

	protected function advise() {
		return 'BEFORE';
	}

	public function handle(\Doku_Event $event) {
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
		$this->modifyDiff($html);
		$this->modifySearch($html);
		$this->modifyMediaManager($html);
		$this->modifyExtensionManager($html);

		$content = $html->save();
		$html->clear();
		unset($html);

		return $content;
	}

	// Add anchors to headings
	private function modifyHeaders($html) {
		global $ACT;

		// only add anchors if a wiki page is shown
		if($ACT != 'show')
			return;

		$headers = array('h1', 'h2', 'h3', 'h4'); // no anchor for h5

		foreach($headers as $header) {
			$selector = $header . '[id]';

			foreach($html->find($selector) as $elm) {
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

			$elm->outertext = '<span class="font-semibold truncate text-gray-700 dark:text-gray-300">' . $path . '</span>'
				.'<a href="' . $href . '" class="btn-icon" data-tooltip-target="' . $id . '" data-tooltip-placement="bottom">'
				.'<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">'
				.'<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />'
				.'</svg>'
				.'</a>'
				.createTooltip($id, $title);
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
			.createTooltip($tooltip_id, '<span id="'.$tooltip_text_id.'"></span>');
	}

	/**
	 * Modifies the diff table by adding / removing some classes, modifying
	 * the revision description and adding a rounded header and footer to the table.
	 */
	private function modifyDiff($html) {
		$diff = $html->find('table.diff', 0);
		if(!$diff)
			return;

		$div = $html->find('div.table', 0);
		$div->addClass('not-prose');
		$div->removeClass('table'); // remove the table class because it intefers with the tailwind class "table"

		if($diff->hasClass('diff_sidebyside'))
			$this->modifyDiffSideBySide($diff);
		else
			$this->modifyDiffInline($diff);

		$diff->innertext = '<div class="diff-header"></div>'
			.$diff->save()
			.'<div class="diff-footer"></div>';
	}

	/**
	 * Modifies the side-by-side diff table by modifying the revision description
	 * and adding classes to some elements.
	 */
	private function modifyDiffSideBySide($diff) {
		// modify the revision descriptions
		foreach($diff->find('th') as $th) {
			// Add a line break after the revision link or before the summary (like it exists in the inline view)
			$sum = $th->find('.sum', 0);
			if($sum) {
				$sum->outertext = '<br />' . str_replace(' – ', '', $sum->outertext);

				if($sum->innertext != ' – ')
					$sum->outertext .= ', ';
			}

			// Remove the line break at the user description
			$user = $th->find('.user', 0);
			if($user)
				$user->outertext = str_replace('<br />', '', $user->outertext);
		}

		// Add classes to the diff lineheaders to style those different dependend on the + or - symbol
		foreach($diff->find('.diff-lineheader') as $elm) {
			if($elm->innertext == '+')
				$elm->addClass('added');
			elseif($elm->innertext == '-')
				$elm->addClass('deleted');
		}

		// Add the "empty" class to empty code blocks.
		foreach($diff->find('td[colspan="2"]') as $elm) {
			if(!_tpl_trim_is_empty($elm->class))
				continue;

			if($elm->innertext == '&#160;')
				$elm->addClass('empty');
		}
	}

	/**
	 * Modifies the inline diff table by modifying the table header.
	 */
	private function modifyDiffInline($diff) {
		// Modify the table header by removing the lineheaders and moving the navgation to the same row.

		$content_nav = '<tr><td colspan="2" class="diffnav"><div class="flex flex-nowrap items-center">';
		foreach($diff->find('.diffnav') as $elm)
			$content_nav .= '<div class="flex-grow">' .  $elm->innertext . '</div>';
		$content_nav .= '</div></td></tr>';

		$content_subnav = '<tr><th colspan="2"><div class="flex flex-nowrap items-center space-x-2">';
		foreach($diff->find('th[!class]') as $elm) {
			$inner = '';

			// determine the innertext by iterating over the nodes and modifying the summary, if necessary
			foreach($elm->nodes as $n) {
				if($n->hasClass('sum')) {
					$n->outertext = str_replace(' – ', '', $n->outertext);

					if($n->innertext != ' – ')
						$n->outertext .= ', ';
				}

				$inner .= $n->save();
			}

			$content_subnav .= '<div class="flex-grow">' .  $inner . '</div>';
		}
		$content_subnav .= '</div></th></tr>';

		// Add the new rows by replacing the first old row
		$diff->find('tr', 0)->outertext = $content_nav . $content_subnav;

		// Remove first 4 rows
		for($i = 1; $i < 4; $i++)
			$diff->find('tr', $i)->outertext = '';

		// Remove the help text at the block headers
		foreach($diff->find('.diff-blockheader') as $elm) {
			$elm->find('.diff-deletedline', 0)->outertext = '';
			$elm->find('.diff-addedline', 0)->outertext = '';
		}
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
			.'<div class="nothing-found">' . $lang['nothingfound']  . '</div>'
			.'</div>';
	}

	/**
	 * modifies the media manager page by adding the "not-prose" class to the file list
	 * and by changing the file list icons.
	 */
	private function modifyMediaManager($html) {
		$mngr = $html->find('#mediamanager__page', 0);
		if(!$mngr)
			return;

		foreach(array('filelist', 'file') as $panel_class)
			$mngr->find('.panel.' . $panel_class, 0)->addClass('not-prose');

		foreach($mngr->find('img') as $img) {
			if($img->src == '/lib/images/plus.gif')
				$img->src = '/lib/tpl/tailwind/icon.php?icon=chevron-right&color=gray-500';
			elseif($img->src == '/lib/images/minus.gif')
				$img->src = '/lib/tpl/tailwind/icon.php?icon=chevron-down&color=gray-500';
		}
	}

	/**
	 * Modifies the list of extensions by wrapping the header and the action list into a header element.
	 * The extension extensions are accessible through a dropdown.
	 */
	private function modifyExtensionManager($html) {
		$mngr = $html->find('#extension__manager', 0);
		if(!$mngr)
			return;

		foreach($mngr->find('.extensionList li') as $elm) {
			$id = $elm->id;

			// determine and cache the button bar and add an ID
			$actions = $elm->find('.actions', 0);
			$popularity = $elm->find('.popularity', 0);

			// determine the button html code
			$btn_html = '';
			if(!_tpl_trim_is_empty($actions->innertext)) {
				$actions->id = $id . '-dropdown';
				$actions->addClass('dropdown-container w-44');
				$actions_html = $actions->save();

				$dropdown_btn = '<button id="' . $id . '-dropdown-button" data-dropdown-toggle="' . $id . '-dropdown" type="button">'
				.'<svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">'
				.'<path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />'
				.'</svg>'
				.'</button>';

				$btn_html .= $dropdown_btn . $actions_html;
			}

			// remove the button bar
			$actions->outertext = '';

			// determine the popularity element and move and remove it from the extension description
			$popularity_html = '';
			if($popularity) {
				$popularity_html .= $popularity->outertext;
				$popularity->outertext = '';
			}

			// set the header element
			$header = $elm->find('h2', 0);
			$header->outertext = '<div class="extension-header">' . $header->outertext . $popularity_html . $btn_html . '</div>';
		}
	}
}
