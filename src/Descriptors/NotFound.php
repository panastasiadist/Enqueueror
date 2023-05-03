<?php

namespace panastasiadist\Enqueueror\Descriptors;

use Exception;
use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class NotFound extends Descriptor {
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request results in
	 * the 404 (not found) error page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public static function get(): array {
		if ( ! is_404() ) {
			return array();
		}

		return static::get_language_enriched_descriptors( array(
			new Description( 'not-found' ),
		) );
	}
}