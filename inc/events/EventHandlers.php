<?php

// Import the events.

$event_dir = __DIR__;

// Super classes
require "$event_dir/EventHandler.php";
require "$event_dir/RevisionRecentOutput.php";

// Event handlers
require "$event_dir/FormRevisionsOutput.php";
require "$event_dir/FormRecentOutput.php";
require "$event_dir/HTMLSecEditButton.php";
require "$event_dir/TPLContentDisplay.php";

/**
 * Class, that handles all DokuWiki events.
 */
class EventHandlers {

	public function __construct() {
		$events = [
			new FormRevisionsOutput,
			new FormRecentOutput,
			new HTMLSecEditButton,
			new TPLContentDisplay, // modify main content
		];

		foreach($events as $event)
			$event->register();
	}

	public static function initialize() {
		static $instance = null;

		if($instance === null)
			$instance = new self;

		return $instance;
	}
}
