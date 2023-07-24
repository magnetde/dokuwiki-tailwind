<?php

// Import the events.

$event_dir = __DIR__;

// Super classes
require "$event_dir/EventHandler.php";

// Event handlers
require "$event_dir/RevisionsRecentsOutput.php";
require "$event_dir/HTMLSecEditButton.php";
require "$event_dir/ContentDisplay.php";

/**
 * Class, that handles all DokuWiki events.
 */
class EventHandlers {

	public function __construct() {
		$events = [
			new RevisionsRecentsOutput,
			new HTMLSecEditButton,
			new ContentDisplay, // modify main content
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
