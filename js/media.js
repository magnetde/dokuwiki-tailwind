// Adds a new click listeners to the navigation tree of the media manager, that adds other icons.
jQuery(() => {
	var tree = jQuery('#media__tree');

	if(!tree.length)
		return;

	// remove old click listeners added by DokuWiki
	tree.off("click");

	tree.dw_tree({
		toggle_selector: 'img',
		load_data: function (show_sublist, img) {
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
});
