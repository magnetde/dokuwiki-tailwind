// Adds a new click listeners to the navigation tree of the media manager, that adds other icons.
function addTreeHandler() {
	var tree = jQuery('#media__tree');

	if(!tree.length)
		return;

	// remove old click listeners added by DokuWiki
	tree.off("click");

	tree.dw_tree({
		toggle_selector: 'img',
		load_data: function(show_sublist, img) {
			// get the enclosed link (is always the first one)
			var $link = img.parent().find('div.li a.idx_dir');

			jQuery.post(
				DOKU_BASE + 'lib/exe/ajax.php',
				$link[0].search.substr(1) + '&call=medians',
				show_sublist,
				'html'
			);
		},

		toggle_display: function(img, opening) {
			console.log(img, opening);
			if(opening)
				img.attr('src', '/lib/tpl/tailwind/icon.php?icon=chevron-down&color=%236b7280');
			else
				img.attr('src', '/lib/tpl/tailwind/icon.php?icon=chevron-right&color=%236b7280');
		},
	});
}

// This function hides the file list, if the file overview is shown.
// Also, the back button is added to the file tab.
function updateMediaContent(mngr) {
	if(fileShown(mngr)) {
		// hide the file list
		mngr.find('.panel.filelist').addClass('a11y');

		// add the back button to the tab panel, if it does not exists
		if(!mngr.find('.panel.file .tabs .back').length) {
			var back = jQuery('<span>').addClass('back').html('←')
				.on('click', () => {
					hideFile(mngr);
				});

			var li = jQuery('<li>').append(back);
			mngr.find('.panel.file .tabs').prepend(li);
		}

		// show the file
		mngr.find('.panel.file').removeClass('a11y');
	}
	else
		hideFile(mngr);
}

// Checks, if the file panel is shown.
function fileShown(mngr) {
	for(var expectedClass of ['tabs', 'panelHeader', 'panelContent'])
		if(mngr.find('.panel.file .' + expectedClass).length)
			return true;

	return false;
}

// Hides the file panel and shows the file list.
function hideFile(mngr) {
	mngr.find('.panel.filelist').removeClass('a11y');
	mngr.find('.panel.file').addClass('a11y').html('');
}

// Adds a observer, that checks, if the file panel has been displayed.
function addFileObserver() {
	var mngr = jQuery('#mediamanager__page');

	if(!mngr.length)
		return;

	updateMediaContent(mngr);

	// Create a observer of the file element
	var observer = new MutationObserver(function(mutations) {
		updateMediaContent(mngr);
	});

	var file = mngr.find('.panel.file').get(0);
	observer.observe(file, {
		childList: true,
	});
}

jQuery(() => {
	addTreeHandler();
	addFileObserver();
});
