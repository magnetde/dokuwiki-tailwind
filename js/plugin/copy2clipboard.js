jQuery(window).on('load', function() {
    if (LANG.plugins.copy2clipboard) {
        jQuery('.dokuwiki .cp2clipcont button').each(function(index) {
            jQuery(this).removeAttr('title');
            jQuery(this).append('<div id="cp2clip-tlp-' + index + '" role="tooltip" class="tooltip-container">' + LANG.plugins.copy2clipboard.title + '\
                <div class="tooltip-arrow" data-popper-arrow=""></div> \
                </div>');

            let $triggerEl = this;
            let $targetEl = jQuery('#cp2clip-tlp-' + index)[0];

            // Options for the tooltip
            let options = {
                placement: 'bottom', // or 'top', 'right', 'left'
                triggerType: 'hover', // or 'click'
            };

            let tooltip = new Tooltip($targetEl, $triggerEl, options);
        });
    }
});