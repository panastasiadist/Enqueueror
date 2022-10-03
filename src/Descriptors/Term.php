<?php

namespace panastasiadist\Enqueueror\Descriptors;

use Exception;
use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class Term extends Descriptor
{
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request is about a
	 * term page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public static function get(): array
	{
		$queried_object = get_queried_object();

		if ( ! $queried_object instanceof \WP_Term ) {
			return array();
		}

		$default_language_object = static::get_default_language_object( $queried_object );

		$descriptors = static::get_language_enriched_descriptors(array(
			new Description( 'term' ),
			new Description( 'term-slug-' . $default_language_object->slug ),
			new Description( 'term-id-' . $default_language_object->term_id ),
			new Description( 'tax-' . $default_language_object->taxonomy ),
			new Description( 'tax-' . $default_language_object->taxonomy . '-term-slug-' . $default_language_object->slug ),
			new Description( 'tax-' . $default_language_object->taxonomy . '-term-id-' . $default_language_object->term_id ),
		));

		if ( $queried_object->term_id !== $default_language_object->term_id ) {
			$current_language_code = static::get_current_language_code();

			$descriptors = array_merge($descriptors, array(
				new Description( 'term-slug-' . $queried_object->slug, 'current', $current_language_code ),
				new Description( 'term-id-' . $queried_object->term_id, 'current', $current_language_code ),
				new Description( 'tax-' . $queried_object->taxonomy . '-term-slug-' . $queried_object->slug, 'current', $current_language_code ),
				new Description( 'tax-' . $queried_object->taxonomy . '-term-id-' . $queried_object->term_id, 'current', $current_language_code ),
			));
		}

		return $descriptors;
	}
}