<?php

namespace panastasiadist\Enqueueror\Descriptors;

use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class Generic extends Descriptor {
	/**
	 * Returns an array of Description instances representing assets that should be loaded irrespective of the content
	 * requested.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public function get(): array {
		return $this->get_language_enriched_descriptions( array(
			new Description( 'global', 'global' ),
		) );
	}
}
