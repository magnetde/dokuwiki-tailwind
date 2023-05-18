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

// Adds listeners to the filelist tabs, that close the file panel when clicked.
function addTabListeners(mngr) {
	mngr.find('.panel.filelist .tabs li')
		.off('click.tab')
		.on('click.tab', () => {
			mngr.find('.panel.file').addClass('a11y');
			hideFile(mngr);
	});
}

// Hides the file panel and shows the file list.
function hideFile(mngr) {
	var filelist = mngr.find('.panel.filelist');
	var file = mngr.find('.panel.file');

	// remove the old file tabs and replace them with disabled elements
	filelist.find('.tabs .file').remove();

	var tabs = ['media_viewtab', 'media_historytab'].map(id => (
		jQuery('<li>').addClass('file').append(
			jQuery('<span>').addClass('disabled').text(LANG.template.tailwind[id])
		)
	));

	filelist.find('.tabs').append(tabs);

	filelist.removeClass('a11y');
	file.addClass('a11y');
}

// This function hides the file list, if the file overview is shown.
// Also, the back button is added to the file tab.
function updateMediaContent(mngr) {
	if(fileShown(mngr)) {
		var filelist = mngr.find('.panel.filelist');
		var file = mngr.find('.panel.file');

		prepareContent(mngr, filelist, file);
		moveDeleteBtn(file);

		// show the file
		file.removeClass('a11y');
	}
	else
		hideFile(mngr);
}

// Checks, if the file panel is shown.
function fileShown(mngr) {
	var file = mngr.find('.panel.file');

	for(var expectedClass of ['tabs', 'panelHeader', 'panelContent'])
		if(file.find('.' + expectedClass).length)
			return true;

	return false;
}

// hides the filelist and moves the file tabs to the filelist tab bar
function prepareContent(mngr, filelist, file) {
	// hide the file list header / content
	filelist.find('.panelHeader, .panelContent').addClass('a11y');

	// remove the old file tabs and replace them with the file tabs of the file panel
	filelist.find('.tabs .file').remove();

	// unselect all filelist tabs by replacing the <strong> element with a <a> element
	filelist.find('.tabs strong').replaceWith(function() {
		// because the active filelist tab is still selected, even if the file list is hidden
		// clicking this tab just hides the file panel

		return jQuery('<a>', { href: '#' })
			.append(jQuery(this).contents())
			.on('click', () => {
				hideFile(mngr);
			});
	});

	var tabs = file.find('.tabs li').clone(true).addClass('file');
	filelist.find('.tabs').append(tabs);
}

// moves the file delete button into the file header
function moveDeleteBtn(file) {
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
}

// Adds a observer, that checks, if the file panel has been displayed.
function addMediaObserver() {
	var mngr = jQuery('#mediamanager__page');

	if(!mngr.length)
		return;

	addTabListeners(mngr);
	updateMediaContent(mngr);

	// overwrite the `init_options` function at the mediamanager object,
	// so the GUI can be recalculated, when the content changed.

	var init_options = dw_mediamanager.init_options;

	dw_mediamanager.init_options = function() {
		init_options(); // call the old function
		updateMediaContent(mngr);
	};
}

jQuery(() => {
	addTreeHandler();
	addMediaObserver();
});
