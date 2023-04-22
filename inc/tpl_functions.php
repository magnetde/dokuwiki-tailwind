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

@require_once dirname(__FILE__) . '/simple_html_dom.php';
@require_once dirname(__FILE__) . '/clsx.php';
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

	return _modify_breadcrumbs($content, $youarehere);
}

function _modify_breadcrumbs($content, $youarehere = false) {
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

/**
 * This function modifies some elements of the main content.
 * Although it is very easy to achieve specific styles for elements by using this function,
 * it is always preferred to change the appearance with CSS.
 */
function _tpl_modify_content($content) {
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

	// Add anchors to headings

	$headers = array('h1', 'h2', 'h3', 'h4', 'h5');

	foreach($headers as $header) {
		foreach($html->find($header) as $elm) {
			$elm->addClass('group');

			$class = clsx("
				ml-3 no-underline opacity-0 transition-opacity group-hover:opacity-100
				text-gray-400 hover:text-gray-600
				dark:text-gray-400 dark:hover:text-gray-200
			");

			$elm->innertext .= '<a class="' . $class . '" href="#' . $elm->id . '">#</a>';
		}
	}

	foreach($html->find('.btn_secedit button') as $elm) {
		$edit_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">'
			.'<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />'
			.'</svg>';

		$elm->innertext = $edit_icon;
	}

	$content = $html->save();

	return $content;
}
