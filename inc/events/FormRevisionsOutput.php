<?php

/**
 * Event handler that modifies the list of the revisions.
 */
class FormRevisionsOutput extends RevisionRecentOutput {

	protected function event() {
		return 'FORM_REVISIONS_OUTPUT';
	}

	protected function advise() {
		return 'BEFORE';
	}

	protected function isRevision() {
		return true;
	}
}
