jQuery(window).on('load', function() {
    // Assuming the active item has a class like 'curid'
    var $scroll = jQuery('.dokuwiki-sidebar').last().parent();
    var $activeItem = $scroll.find('.acmenu span.curid').last(); 

    if ($activeItem.length) {
        var $sidebarScrollTop = jQuery('.dokuwiki-sidebar').last().scrollTop(); // Current scroll position of the sidebar
        var $itemOffsetTop = $activeItem.position().top; // Position of the active item relative to the sidebar

        // Scroll to active item only if it's not visible on sidebar
        if ($itemOffsetTop > $scroll.height()) {
            $scroll.scrollTop($itemOffsetTop + $sidebarScrollTop);
        }
    }
});