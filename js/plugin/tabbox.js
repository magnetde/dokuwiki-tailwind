jQuery(window).on('load', function() {
    jQuery('.plugin_tabbox ul.tabs li a').each(function() {
        // Remove "#Edit [Tab Name]" text from tab header
        let originalText = jQuery(this).text();
        let lastHashIndex = originalText.lastIndexOf('#');

        if (lastHashIndex !== -1) {
            let newText = originalText.substring(0, lastHashIndex);
            jQuery(this).text(newText);
        }
    });
});