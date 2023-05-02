jQuery(() => {
	var searchForm = jQuery('.search-results-form');
	if(!searchForm.length)
		return;

	searchForm.find('.toggleAssistant').text(LANG.template.tailwind.search_filter);
});
