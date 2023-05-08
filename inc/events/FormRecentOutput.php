<?php

/**
 * Event handler that modifies the list of the recent changes.
 */
class FormRecentOutput extends RevisionRecentOutput {

	protected function event() {
		return 'FORM_RECENT_OUTPUT';
	}

	protected function advise() {
		return 'BEFORE';
	}

	protected function isRevision() {
		return false;
	}
}
