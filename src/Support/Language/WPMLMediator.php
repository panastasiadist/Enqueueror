<?php

namespace panastasiadist\Enqueueror\Support\Language;

use WP_Post;
use WP_Term;
use panastasiadist\Enqueueror\Interfaces\LanguageMediatorInterface;

class WPMLMediator implements LanguageMediatorInterface {
	public function get_language_code( bool $is_default ): string {
		$value = apply_filters( $is_default ? 'wpml_default_language' : 'wpml_current_language', '' );

		return is_string( $value ) ? $value : '';
	}

	public function get_default_language_object( $queried_object ) {
		if ( $queried_object instanceof WP_Term ) {
			$default_queried_object = $this->map_default_language_object(
				$queried_object->term_id,
				$queried_object->taxonomy,
				function ( int $default_id ) use ( $queried_object ) {
					return get_term( $default_id, $queried_object->taxonomy );
				}
			);
		} else if ( $queried_object instanceof WP_Post ) {
			$default_queried_object = $this->map_default_language_object(
				$queried_object->ID,
				$queried_object->post_type,
				function ( int $default_id ) use ( $queried_object ) {
					return get_post( $default_id, $queried_object->post_type );
				}
			);
		}

		return $default_queried_object ?? $queried_object;
	}

	public function is_supported(): bool {
		return defined( 'ICL_SITEPRESS_VERSION' );
	}

	/**
	 * Maps a given object to its equivalent object in the default language, based on its ID.
	 *
	 * @param int $object_id The object's ID for which to return its equivalent in the default language.
	 * @param string $object_type The object's type to be supplied to WPML.
	 * @param callable $object_mapper_fn A function that retrieves the actual object in the default language.
	 *
	 * @return mixed|null The equivalent object in the default language, or null if it cannot be determined.
	 */
	private function map_default_language_object( int $object_id, string $object_type, callable $object_mapper_fn ) {
		$default_language_code = $this->get_language_code( true );
		$current_language_code = $this->get_language_code( false );

		if ( ! ( $default_language_code && $current_language_code ) ) {
			return null;
		}

		$default_id = apply_filters( 'wpml_object_id', $object_id, $object_type, true, $default_language_code );

		if ( $default_id !== $object_id ) {
			do_action( 'wpml_switch_language', $default_language_code );
			$queried_object = $object_mapper_fn( $default_id );
			do_action( 'wpml_switch_language', $current_language_code );
		}

		return $queried_object ?? null;
	}
}
