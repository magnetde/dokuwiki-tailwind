<?php
/**
 * Template Functions
 *
 * This file provides template specific custom functions that are
 * not provided by the DokuWiki core.
 * It is common practice to start each function with an underscore
 * to make sure it won't interfere with future core functions.
 */

// must be run from within DokuWiki
if(!defined('DOKU_INC'))
	die();

@require_once dirname(__FILE__) . '/global.php';
use simple_html_dom\simple_html_dom;

/**
 * Wrapper to return the avatar of an user.
 */
function _tpl_getavatar($username, $email, $size = 80, $d = 'mm', $r = 'g') {
	global $INFO;

	$email = strtolower(trim($email));

	$avatar_url = "https://www.gravatar.com/avatar/";
	$avatar_url .= md5($email);
	$avatar_url .= "?s=$size&d=$d&r=$r";

	$media_link = ml("$avatar_url&.jpg", ['cache' => 'recache', 'w' => $size, 'h' => $size]);
	return $media_link;
}

/**
 * Wrapper to create a search form.
 * This function overwrites the search button.
 *
 * The search input cannot be modified by using events.
 */
function _tpl_searchform() {
	// Capture the original search form
	ob_start();
	tpl_searchform($autocomplete = false);
	$content = ob_get_clean();

	// Parse the html
	$html = new simple_html_dom;
	$html->load($content, true, false);

	// Return original content if Simple HTML DOM fail
	if(!$html) {
		return $content;
	}

	// Remove the button and turn it into an icon
	foreach($html->find('button') as $elm) {
		$elm->outertext = _tpl_search_input();
	}

	$content = $html->save();

	return $content;
}

/**
 * Returns the text field icon.
 */
function _tpl_search_input() {
	// ugly code, probably needs a fix
	return '<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">'
        .'<svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">'
		.'<path fill-rule="evenodd" d='
		.'"M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"'
		.' clip-rule="evenodd">'
		.'</path></svg>'
      	.'</div>';
}

/**
 * Function to create breadcrumbs or the "you are here" list.
 *
 * The breadcrumbs cannot be modified by using events.
 *
 * Rather create the breadcrumb list without "tpl_breadcrumbs" / "tpl_youarehere" and modifying the DOM.
 */
function _tpl_breadcrumbs($youarehere = false) {
	$sep = '<svg aria-hidden="true" class="inline w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">'
		.'<path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd">'
		.'</path></svg>';

	// Capture the output
	ob_start();

	if(!$youarehere) {
		tpl_breadcrumbs($sep = $sep);
	} else {
		tpl_youarehere($sep = $sep);
	}

	$content = ob_get_clean();

	// Import HTML string
	$html = new simple_html_dom;
	$html->load($content, true, false);

	// Return original content if Simple HTML DOM fail or exceeded page size (default MAX_FILE_SIZE => 600KB)
	if(!$html) {
		return $content;
	}

	// Get title and remove the node
	$elm = $html->find('.bchead', 0);
	$title = $elm->outertext;
	$elm->remove();

	// Remove first separator
	$sep = $html->find('.bcsep', 0);
	if($sep)
		$sep->remove();

	$body = '';
	if(!$youarehere) {
		foreach($html->childNodes() as $elm) {
			$body = $elm->outertext . $body;
		}
	} else {
		$body = $html->save();
	}

	return $title . $body;
}
