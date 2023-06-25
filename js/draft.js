// Because this template uses another draft lock element, we need to change it.
// See 'locktimer.js' in the DokuWiki repo for the function 'addRefreshCallback'.
dw_locktimer.addRefreshCallback((data) => {
	var icon = jQuery('#draft-icon');

	icon.removeClass('empty');
	if(data.errors.length)
		icon.addClass('draft-error');

	jQuery('#draft-status-text').html(data.draft);
});
