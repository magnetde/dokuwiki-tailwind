<?php

/**
 * Abstract class used to implement all event handlers, that modifies content,
 * that is not archievable by using plain CSS.
 */
abstract class Event {

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
	abstract public function handler(\Doku_Event $event);

	/**
	 * Registers the event at the DokuWiki event handlers.
	 */
	public function register() {
		global $EVENT_HANDLER;

		$EVENT_HANDLER->register_hook($this->event(), $this->advise(), $this, 'handler');
	}
}
