jQuery(() => {
	// Edit the search header
	var form = jQuery('.search-results-form');

	if(!form.length)
		return;

	form.find('.toggleAssistant').text(LANG.template.tailwind.search_filter);

	// Add handlers to the search tabs
	var box = jQuery('.search-box');

	box.find('#tab-quickhits').on('click', () => {
		box.find('#tab-quickhits').addClass('active');
		box.find('#tab-content-quickhits').addClass('active');

		box.find('#tab-fulltext').removeClass('active');
		box.find('#tab-content-fulltext').removeClass('active');
	});

	box.find('#tab-fulltext').on('click', () => {
		box.find('#tab-quickhits').removeClass('active');
		box.find('#tab-content-quickhits').removeClass('active');

		box.find('#tab-fulltext').addClass('active');
		box.find('#tab-content-fulltext').addClass('active');
	});
});
