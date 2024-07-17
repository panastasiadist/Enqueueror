<?php

namespace panastasiadist\Enqueueror\Descriptors;

use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;
use WP_Post;

class Post extends Descriptor {
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request is about a
	 * post type based page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public function get(): array {
		$queried_object = get_queried_object();

		if ( ! $queried_object instanceof WP_Post ) {
			return array();
		}

		$default_language_object = $this->language_mediator->get_default_language_object( $queried_object );

		$descriptors = $this->get_language_enriched_descriptors( array(
			new Description( 'type' ),
			new Description( 'type-id-' . $default_language_object->ID ),
			new Description( 'type-slug-' . $default_language_object->post_name ),
			new Description( 'type-' . $default_language_object->post_type ),
			new Description( 'type-' . $default_language_object->post_type . '-slug-' . $default_language_object->post_name ),
			new Description( 'type-' . $default_language_object->post_type . '-id-' . $default_language_object->ID ),
		) );

		if ( $queried_object->ID !== $default_language_object->ID ) {
			$current_language_code = $this->language_mediator->get_language_code( false );

			$descriptors = array_merge( $descriptors, array(
				new Description( 'type-id-' . $queried_object->ID, 'current', $current_language_code ),
				new Description( 'type-slug-' . $queried_object->post_name, 'current', $current_language_code ),
				new Description( 'type-' . $queried_object->post_type . '-slug-' . $queried_object->post_name, 'current', $current_language_code ),
				new Description( 'type-' . $queried_object->post_type . '-id-' . $queried_object->ID, 'current', $current_language_code ),
			) );
		}

		return $descriptors;
	}
}
