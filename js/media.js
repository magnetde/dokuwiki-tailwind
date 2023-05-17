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
			var link = img.parent().find('div.li a.idx_dir');

			jQuery.post(
				DOKU_BASE + 'lib/exe/ajax.php',
				link[0].search.substr(1) + '&call=medians',
				show_sublist,
				'html'
			);
		},

		toggle_display: function(img, opening) {
			// TODO: handle dark color theme
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
		var filelist = mngr.find('.panel.filelist');
		var file = mngr.find('.panel.file');

		// hide the file list header / content
		filelist.find('.panelHeader, .panelContent').addClass('a11y');

		// remove the old file tabs and replace them with the file tabs of the file panel
		filelist.find('.tabs .file').remove();

		var tabs = file.find('.tabs li').clone(true).addClass('file');
		console.log(tabs);
		filelist.find('.tabs').append(tabs);

		// move the file delete button to the panel header
		var actions = file.find('.panelContent .actions');
		if(actions.length) {
			// cache the button element
			var btn = actions.find('#mediamanager__btn_delete');

			// remove the action element, the button gets staled
			actions.remove();

			// append button to header
			file.find('.panelHeader').append(btn);
		}

		// show the file
		file.removeClass('a11y');
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
	var filelist = mngr.find('.panel.filelist');
	var file = mngr.find('.panel.file');

	// add the disabled file panels to the regular tab panel, if they do not exist
	if(!file.find('.tabs .file').length) {
		var tabs = ['media_viewtab', 'media_historytab'].map(id => (
			jQuery('<li>').addClass('file').append(
				jQuery('<span>').addClass('disabled').text(LANG.template.tailwind[id])
			)
		));

		filelist.find('.tabs').append(tabs);
	}

	filelist.removeClass('a11y');
	file.addClass('a11y');
}

// Adds a observer, that checks, if the file panel has been displayed.
function addMediaObserver() {
	var mngr = jQuery('#mediamanager__page');

	if(!mngr.length)
		return;

	updateMediaContent(mngr);

	// Create a observer of the file element
	var fileListObserver = new MutationObserver(() => { hideFile(mngr); });
	fileListObserver.observe(mngr.find('.panel.filelist').get(0), { childList: true });

	var fileObserver = new MutationObserver(() => { updateMediaContent(mngr); });
	fileObserver.observe(mngr.find('.panel.file').get(0), { childList: true });
}

jQuery(() => {
	addTreeHandler();
	addMediaObserver();
});
