// Because this template uses another draft lock element, we need to change it.
// See 'locktimer.js' in the DokuWiki repo for the function 'addRefreshCallback'.
dw_locktimer.addRefreshCallback((data) => {
	var color;
	if(!data.errors.length)
		color = 'bg-green-500';
	else
		color = 'bg-red-500';

	var img = `<span class="flex w-3 h-3 mr-2 ${color} rounded-full"></span>`;

	jQuery('#draft-icon').html(img);
	jQuery('#draft-status-text').html(data.draft);
});
