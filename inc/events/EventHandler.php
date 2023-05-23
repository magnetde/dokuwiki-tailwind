<?php

require_once __DIR__ . '/util.php'; // Include the utility functions

/**
 * Abstract class used to implement all event handlers, that modifies content,
 * that is not archievable by using plain CSS.
 */
abstract class EventHandler {

	function __construct() {}

	/**
	 * Returns the event name, like defined in https://www.dokuwiki.org/devel:events_list
	 */
	abstract protected function event();

	/**
	 * Must return either 'BEFORE' or 'AFTER'.
	 */
	abstract protected function advise();

	/**
	 * The event handler function.
	 */
	abstract public function handle(\Doku_Event $event);

	/**
	 * Registers the event at the DokuWiki event handlers.
	 */
	public function register() {
		global $EVENT_HANDLER;

		$EVENT_HANDLER->register_hook($this->event(), $this->advise(), $this, 'handle');
	}
}
