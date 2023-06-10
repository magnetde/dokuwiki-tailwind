<?php

use simple_html_dom\simple_html_dom;

/**
 * Abstract handler class used to modify the revision and recent list.
 * This event handler does not apply styles because styles are applied by CSS.
 */
abstract class RevisionRecentOutput extends EventHandler {

	/**
	 * Returns as a boolean, whether the current handler is used for the revision or the recent list.
	 */
	abstract protected function isRevision();

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
		global $lang;

		if(_tpl_trim_is_empty($content))
			return $content;

		$html = new simple_html_dom;
		$html->load($content, true, false);

		$recent_page = !$this->isRevision();

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
		if($recent_page)
			$content .= '<span class="subtitle-name">' . $revlink->innertext . '</span>, ';

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
		if($revlink && !$revlink->hasClass('wikilink2')) {
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
}
