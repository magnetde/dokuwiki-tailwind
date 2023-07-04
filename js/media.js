jQuery(() => {
	addTreeHandler();
	addMediaHandler();
});

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
				img.attr('src', DOKU_TPL + 'icon.php?icon=chevron-down&color=gray-500');
			else
				img.attr('src', DOKU_TPL + 'icon.php?icon=chevron-right&color=gray-500');
		},
	});
}

// Setup, that the media content is updated if it changes.
function addMediaHandler() {
	// Media manager fullscreen page
	var media = jQuery('#mediamanager__page');
	if(media.length) {
		modifyMediaContent(media);

		mediaManagerInit(() => {
			modifyMediaContent(media);
			modifyMediaRevisions(media);
		});

		return;
	}

	// Media manager popup
	media = jQuery('#media__manager');
	if(media.length) {
		modifyMediaPopup(media);

		mediaManagerInit(() => {
			modifyMediaPopup(media);
		});
	}
}

// Overwrites the `init_options` function at the mediamanager object,
// so the GUI can be recalculated, when the content changed.
function mediaManagerInit(cb) {
	// save the old version
	var init_options = dw_mediamanager.init_options;

	dw_mediamanager.init_options = function() {
		init_options(); // call the old function
		cb();
	};
}

// This function refreshes the media content if it has been changed (at page start or after tab change).
// Also, the events are added to the filelist tabs.
function modifyMediaContent(mngr) {
	var filelist = mngr.find('.panel.filelist');
	var file = mngr.find('.panel.file');

	// Hide the file panel if the filelist tabs are clicked.
	filelist.find('.tabs li')
		.off('click.tab')
		.on('click.tab', () => hideFile(mngr));

	if(fileShown(file)) {
		prepareContent(mngr, filelist, file);
		moveActions(file);

		// show the file
		file.removeClass('a11y');
	}
	else
		hideFile(mngr);
}

// Checks, if the file panel is shown.
function fileShown(file) {
	for(var expectedClass of ['tabs', 'panelHeader', 'panelContent'])
		if(file.find('.' + expectedClass).length)
			return true;

	return false;
}

// hides the filelist and moves the file tabs to the filelist tab bar
function prepareContent(mngr, filelist, file) {
	// hide the filelist header / content
	filelist.find('.panelHeader, .panelContent').addClass('a11y');

	// remove the old file tabs to replace them with the file tabs of the file panel
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

	// Clone the file tabs and add the 'details' event to them
	// TODO: clicking on a tab at the file list reloads the file list.
	// This leads to two ajax calls to the backend, where the second is not necessary.
	file.find('.tabs li').clone()
		.addClass('file')
		.on('click', 'a', dw_mediamanager.details)
		.appendTo(filelist.find('.tabs'));
}

// moves the actions into the file header
function moveActions(file) {
	// move the file delete button to the panel header
	var actions = file.find('.panelContent .actions');
	if(actions.length) {
		// selectors for children to remove
		var remove = [':has(#mediamanager__btn_update)'].join(',');

		// Filter and cache all children
		var children = actions.children().not(remove);

		// remove the action list
		actions.remove();

		// awrap children inside an list and ppend button to header
		file.find('.panelHeader').append(
			jQuery('<ul>').addClass('actions').append(children),
		);
	}
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
	file.addClass('a11y').html('');
}

function modifyMediaRevisions(mngr) {
	var revs = mngr.find('#page__revisions');
	if(!revs.length)
		return;

	revs.addClass('not-prose');

	revs.find('div.li').replaceWith(function() {
		return modifyChange(jQuery(this));
	});
}

// Modifies a single change element, so the change styles can be applied.
// This function works similar as the `modifyRevision` in RevisionRecentOutput.php.
// If this function should be changed, the function `modifyRevision` must also be adapted.
function modifyChange(div) {
	// Check if the revision is already modified.
	// This is needed, because of a bug at the tab panel, that always reloads the file list (see `prepareContent()`).
	if(div.find('.revision-info').length)
		return div;

	// First collect the elements
	var input = div.find('input');

	var date = div.find('span.date');
	var diff_link = div.find('a.diff_link'); // may not exist

	// preview contains a description of the page
	var preview = div.find('a.wikilink1');
	if(!preview.length) // revlink is either of class wikilink1 or wikilink2
		preview = div.find('a.wikilink2');

	var summary = div.find('span.sum');
	var user = div.find('span.user');
	var sizechange = div.find('span.sizechange');

	var hasPreview = preview.length && !preview.hasClass('wikilink2');

	if(hasPreview) {
		// if preview exists: create a preview link with the class of the summary element
		// and the text and href of the preview element

		var sum = jQuery('<a>')
			.addClass(summary.attr('class'))
			.attr('href', preview.attr('href'))
			.text(preview.text());

		summary = sum;
	} else
		summary.addClass('empty').text('-');

	// Then reorder them
	var revinfo = jQuery('<div>').addClass('revision-info');

	revinfo.append(
		// Add the left content
		jQuery('<div>').addClass('rev-description')
			.append(
				jQuery('<div>').addClass('summary').append(summary, sizechange), // first line
				jQuery('<span>').addClass('subtitle').append(date, ', ', user),
			),
	);

	// Add the right content
	var btns = jQuery('<div>').addClass('revision-buttons').attr('role', 'group');

	// Modify the diff link, if exists
	if(diff_link.length) {
		diff_link.html('');
		btns.append(diff_link);
	}

	revinfo.append(btns);

	return jQuery('<div>').addClass('li').append(input, revinfo);
}

// Modifies the media popup content by replacing the file action icons.
function modifyMediaPopup(mngr) {
	var content = mngr.find('#media__content');
	if(content.length) {
		popupReplaceIconButton(content, '/lib/images/magnifier.png', 'btn-open');
		popupReplaceIconButton(content, '/lib/images/mediamanager.png', 'btn-manager');
		popupReplaceIconButton(content, '/lib/images/trash.png', 'btn-trash');
	}
}

// Replaces all image elements, with the specific source path with div elements with the given class.
function popupReplaceIconButton(content, src, cls) {
	content.find(`img[src="${src}"]`).each(function() {
		var img = jQuery(this);
		var div = jQuery('<div>').addClass('img-icon').addClass(cls);

		var title = img.attr('title');
		if(title) {
			var parent = img.parent();

			// fix the tooltip text
			if(parent.length && parent.prop('nodeName') === 'a')
				parent.attr('title', title);
			else
				div.attr('title', title);
		}

		img.replaceWith(div);
	});
}
