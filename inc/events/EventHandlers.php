<?php

// Import the events.
spl_autoload_register(function($class_name) {
	include tpl_incdir() . 'inc/events/' . $class_name . '.php';
});

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
