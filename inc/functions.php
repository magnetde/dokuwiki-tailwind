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
 * Checks if the string $string has the prefix $prefix.
 */
function _tpl_has_prefix($str, $prefix) {
	return substr($str, 0, strlen($prefix)) == $prefix;
}

/**
 * Removes the prefix $prefix from string $string.
 * If $string has no prefix $prefix, $string is returned.
 */
function _tpl_remove_prefix($str, $prefix) {
	if(_tpl_has_prefix($str, $prefix))
		$str = substr($str, strlen($prefix));

	return $str;
}

/**
 * Returns, if the trimmed string is empty.
 */
function _tpl_trim_is_empty($str) {
	return strlen(trim($str)) == 0;
}

/**
 * If the current page either shows text, is an admin page or a plugin page,
 * this function returns a list of classes (as a single string),
 * that exactly describes the current page.
 */
function _tpl_page_classes() {
	global $ACT;
	global $INPUT;

	$class = '';

	if($ACT == 'admin') {
		$class .= 'dw-action-' . $ACT;

		$page = $INPUT->str('page');
		if($page)
			$class .= ' dw-page-' . $page;
	} elseif(_tpl_has_prefix($ACT, 'plugin_'))
		$class .= 'dw-action-plugin ' . _tpl_remove_prefix($ACT, 'plugin_');
	else
		$class .= 'dw-action-' . $ACT;

	return $class;
}

/**
 * Prints the DokuWiki meta headers by removing the css.php style sheet.
 */
function _tpl_metaheaders() {
	// Capture the original meta headers
	ob_start();
	tpl_metaheaders();
	$content = ob_get_clean();

	// Parse the html
	$html = new simple_html_dom;
	$html->load($content, true, false);

	if($html) {
		// Remove the button and turn it into an icon
		foreach($html->find('link[rel="stylesheet"]') as $elm)
			if(_tpl_has_prefix($elm->href, '/lib/exe/css.php'))
				// $elm->outertext = ''; // remove css.php stylesheet

		$content = $html->save();
		$html->clear();
	}

	unset($html);
	echo $content;
}

/**
 * Wrapper to print a search form.
 * This function overwrites the search button.
 *
 * The search input cannot be modified by using events.
 */
function _tpl_searchform() {
	// Capture the original search form
	ob_start();
	tpl_searchform($autocomplete = true);
	$content = ob_get_clean();

	// Parse the html
	$html = new simple_html_dom;
	$html->load($content, true, false);

	// Remove the button and turn it into an icon
	foreach($html->find('button') as $elm)
		$elm->outertext = _tpl_search_input();

	// Disable autocomplete on the search input
	foreach($html->find('input[type="text"]') as $elm)
		$elm->autocomplete = 'off';
		$elm->title = '[K]';
		$elm->accesskey = 'k';

	$content = $html->save();
	$html->clear();
	unset($html);

	echo $content;
}

/**
 * Returns the text field icon.
 */
function _tpl_search_input() {
    ob_start(); ?>
<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
	<svg class="w-5 h-5 text-gray-400 dark:text-gray-500" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
		<path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd">
	</path></svg>
</div>
<div class="absolute inset-y-0 right-0 mr-3 flex items-center pointer-events-none">
    <kbd class="hidden font-sans text-xs/4 text-gray-500 dark:text-gray-400 [.os-macos_&amp;]:block">⌘K</kbd>
    <kbd class="hidden font-sans text-xs/4 text-gray-500 not-[.os-macos_&amp;]:block dark:text-gray-400">Ctrl&nbsp;K</kbd>
</div>
	<?php return ob_get_clean();
}

/**
 * Prints the user avatar element.
 */
function _tpl_avatar() {
	global $INFO;

	if(array_key_exists('REMOTE_USER', $_SERVER)) {
		$user = $_SERVER['REMOTE_USER'];
		$avatar_url = _tpl_avatarURL($user, $INFO['userinfo']['mail']);
		echo '<img class="w-8 h-8 rounded-full" src="' . $avatar_url . '" alt="' . $user . '">';
	} else { ?>
<div class="relative w-8 h-8 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
	<svg class="absolute w-8 h-8 text-gray-400 top-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
		<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
	</path></svg>
</div>
	<?php }
}

/**
 * Wrapper to return the avatar of an user.
 */
function _tpl_avatarURL($username, $email, $size = 80, $d = 'mm', $r = 'g') {
	global $INFO;

	$email = strtolower(trim($email));

	$avatar_url = "https://www.gravatar.com/avatar/";
	$avatar_url .= md5($email);
	$avatar_url .= "?s=$size&d=$d&r=$r";

	$media_link = ml("$avatar_url&.jpg", ['cache' => 'recache', 'w' => $size, 'h' => $size]);
	return $media_link;
}

/**
 * Returns the user info, must only be called when the current user is logged in.
 */
function _tpl_userInfo() {
	global $INFO;

	echo '<div class="px-4 py-3">'
		.'<span class="text-primary block text-sm">' . hsc($INFO['userinfo']['name']) . '</span>'
		.'<span class="text-secondary block text-sm truncate">' . $INFO['userinfo']['mail'] . '</span>'
		.'</div>';
}

/**
 * This function prints the sidebar by modifying lists, so they appear as nav items.
 */
function _tpl_sidebar() {
	global $conf;

	// Get the original sidebar
	$content = tpl_include_page($conf['sidebar'], false, true);

	// Import HTML string
	$html = new simple_html_dom;
	$html->load($content, true, false);

	// iterate over lists
	foreach($html->find('ul, div.level1 > ul') as $ul) {
		if($ul->find('.node', 0)) // ignore, if sublists exist
			continue;
		if(!$ul->find('a', 0)) // ignore, if no links exist
			continue;

		// add class to the list
		$ul->addClass('sidebar-nav-list');

		foreach($ul->find('.li') as $li) {
			$item = $li->firstChild();
			if($item->tag != 'a')
				$item = $li;

			// add class to list elements
			$item->addClass('sidebar-nav-item');
		}
	}

	$content = $html->save();
	$html->clear();
	unset($html);

	echo $content;
}

/**
 * Function to create breadcrumbs or the "you are here" list.
 *
 * The breadcrumbs cannot be modified by using events.
 *
 * Rather create the breadcrumb list without "tpl_breadcrumbs" / "tpl_youarehere" and modifying the DOM.
 */
function _tpl_breadcrumbs($youarehere = false) {
	if($youarehere)
		$sep = '<svg aria-hidden="true" class="inline w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">'
			.'<path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd">'
			.'</path></svg>';
	else
		$sep = '<span class="mx-0.5">・</span>';

	// Capture the output
	ob_start();

	if(!$youarehere)
		tpl_breadcrumbs($sep = $sep);
	else
		tpl_youarehere($sep = $sep);

	$content = ob_get_clean();

	// Import HTML string
	$html = new simple_html_dom;
	$html->load($content, true, false);

	// Return original content if Simple HTML DOM fail or exceeded page size (default MAX_FILE_SIZE => 600KB)
	if(!$html)
		return $content;

	// Get title and remove the node
	$elm = $html->find('.bchead', 0);
	$title = $elm->outertext;
	$elm->remove();

	// Remove first separator
	$first_sep = $html->find('.bcsep', 0);
	if($first_sep)
		$first_sep->remove();

	$body = '';
	if(!$youarehere) {
		foreach($html->childNodes() as $elm) {
			$body = $elm->outertext . $body;
		}
	} else
		$body = $html->save();

	$html->clear();
	unset($html);

	return $title . $body;
}

/**
 * Prints a table of content, that wotks with Bootstrap scrollspy.
 */
function _tpl_getTOC() {
	ob_start();
	tpl_toc();
	$content = ob_get_clean();

	$html = new simple_html_dom;
	$html->load($content, true, false);

	$header = $html->find('h3', 0);
	if($header) { // remove() does not work or else the other find() operations do not work anymore
		$header->outertext = '';
	}

	// Add the nav class to each ul element
	foreach($html->find('ul') as $elm)
		$elm->addClass('nav');

	// Unwrap div.li element
	foreach($html->find('div.li') as $elm) {
		$link = $elm->find('a', 0);
		if($link)
			$link->addClass('nav-link');

		$elm->outertext = str_replace(['<div class="li">', '</div>'], '', $elm->save());
	}

	$root = $html->find('ul.toc', 0); // first element
	if($root) {
		$content = '<nav id="dw__toc" class="dw__toc" role="navigation">';
		$content .= $root->save();
		$content .= '</nav>';
	}

	$html->clear();
	unset($html);

	return $content;
}

/**
 * Prints the media tree with modified collapse icons.
 */
function _tpl_mediaTree() {
	ob_start();
	tpl_mediaTree();
	$content = ob_get_clean();

	// TODO: this can be done just with string replacement

	$html = new simple_html_dom;
	$html->load($content, true, false);

	$tree = $html->find('#media__tree', 0);
	if($tree) {
		foreach($tree->find('img') as $img) {
			if($img->src == '/lib/images/plus.gif')
				$img->src = '/lib/tpl/tailwind/icon.php?icon=chevron-right&color=gray-500';
			elseif($img->src == '/lib/images/minus.gif')
				$img->src = '/lib/tpl/tailwind/icon.php?icon=chevron-down&color=gray-500';
		}
	}

	$content = $html->save();
	$html->clear();
	unset($html);

	echo $content;
}
