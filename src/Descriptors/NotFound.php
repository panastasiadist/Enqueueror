<?php

namespace panastasiadist\Enqueueror\Descriptors;

use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class NotFound extends Descriptor {
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request results in
	 * the 404 (not found) error page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public function get(): array {
		if ( ! is_404() ) {
			return array();
		}

		return $this->get_language_enriched_descriptions( array(
			new Description( 'not-found' ),
		) );
	}
}
