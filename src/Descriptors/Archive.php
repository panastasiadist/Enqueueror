<?php

namespace panastasiadist\Enqueueror\Descriptors;

use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;
use WP_Post_Type;

class Archive extends Descriptor {
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request is about an
	 * archive page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public function get(): array {
		if ( ! is_archive() ) {
			return array();
		}

		$descriptors = array(
			new Description( 'archive' ),
		);

		$queried_object = get_queried_object();

		if ( is_date() ) {
			$descriptors[] = new Description( 'archive-date' );
		} else if ( $queried_object instanceof WP_Post_Type ) {
			$descriptors[] = new Description( 'archive-type-' . $queried_object->name );
		}

		return $this->get_language_enriched_descriptions( $descriptors );
	}
}
