<?php

use simple_html_dom\simple_html_dom;

/**
 * Event handler that modifies the list of the revisions or recents.
 * This event handler does not apply styles because styles are applied by CSS.
 */
class RevisionsRecentsOutput extends EventHandler {

	protected function event() {
		return array('FORM_REVISIONS_OUTPUT', 'FORM_RECENT_OUTPUT');
	}

	protected function advise() {
		return 'BEFORE';
	}

	/**
	 * Handler function.
	 */
	public function handle(\Doku_Event $event) {
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
	 * If the revision is a revision at the recent page,
	 * the second parameter must be true.
	 * Styles are applied with CSS.
	 * 
	 * If this function should be changed, the function `modifyChange` (media.js) must also be adapted.
	 */
	private function modifyRevision($content) {
		global $ACT, $lang;

		if(_tpl_trim_is_empty($content))
			return $content;

		$html = new simple_html_dom;
		$html->load($content, true, false);

		$recent_page = $ACT === 'recent';
		$media_page = $ACT === 'media';

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

		// preview contains a description of the page
		$preview = $html->find('a.wikilink1', 0);
		if(!$preview) // revlink is either of class wikilink1 or wikilink2
			$preview = $html->find('a.wikilink2', 0);

		$summary = $html->find('span.sum', 0);
		$user = $html->find('span.user', 0);
		$sizechange = $html->find('span.sizechange', 0);

		// Revisions or recent
		if(!$media_page) {
			// Modify the summary text, if it is empty
			$summary_text = _tpl_remove_prefix($summary->innertext, ' – ');

			if(empty($summary_text)) {
				$summary_text = tpl_getLang('no_description');
				$summary->addClass('empty');
			}
		}

		// Preview exists
		$has_preview = $preview && !$preview->hasClass('wikilink2');

		if(!$media_page)
			$summary->innertext = $summary_text;
		elseif($has_preview)
			$summary->innertext = $preview->innertext;

		// If a preview link exists, convert the summary text to a link
		if($has_preview) {
			$summary->tag = 'a';
			$summary->href = $preview->href;
		}

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
		if($recent_page)
			$content .= '<span class="subtitle-name">' . $preview->innertext . '</span>, ';

		$content .= $date . ', ' .$user
			.'</span>'
			.'</div>';

		// Add the right content
		$content .= '<div class="revision-buttons" role="group">';

		// Modify the diff link, if exists
		if($diff_link) {
			// Set the tooltip of the element
			$this->setTitle($diff_link);

			$diff_link->innertext = '';
			$content .= $diff_link->save();
		}

		// Add revisions list
		if($recent_page) {
			// Set the title of the element
			$this->setTitle($revisions_link);

			$revisions_link->innertext = '';
			$content .= $revisions_link->save();
		}

		$content .= '</div>';

		// Wrap around a diff element, so we can apply styles like padding for all children
		$content = '<div class="revision-info">' . $content . '</div>';

		// Add a gray dot to the vertical line
		if($recent_page) {
			$content = '<div class="'
				.clsx("
					absolute w-2 h-2 rounded-full mt-[1.4rem] -left-1
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

	/**
	 * This function finds the first child element, that contains a title attribute and then
	 * sets the title of this element to the value of the child title.
	 */
	private function setTitle($elm) {
		$first = $elm->find('[title]', 0);
		if($first && $first->title)
			$elm->title = $first->title;
	}
}
