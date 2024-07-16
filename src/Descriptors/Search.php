<?php

namespace panastasiadist\Enqueueror\Descriptors;

use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class Search extends Descriptor {
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request is about the
	 * search page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public function get(): array {
		if ( ! is_search() ) {
			return array();
		}

		return $this->get_language_enriched_descriptors( array(
			new Description( 'search' ),
		) );
	}
}
