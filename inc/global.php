<?php

/**
 * This file imports all other template library code and should only be included with "@require_once".
 */

$template_dir = tpl_incdir();

// Load helper functions.
require "$template_dir/inc/functions.php";
require "$template_dir/inc/clsx.php";
require "$template_dir/inc/icons.php";

// Add the event handlers
require "$template_dir/inc/events/EventHandlers.php";
EventHandlers::initialize();

// Load PHP Simple HTML DOM class
require "$template_dir/inc/vendor/simple_html_dom.php";
