<?php

namespace panastasiadist\Enqueueror\Support\Language;

use WP_Post;
use WP_Term;
use panastasiadist\Enqueueror\Interfaces\LanguageMediatorInterface;

class PolylangMediator implements LanguageMediatorInterface {
	public function get_language_code( bool $is_default ): string {
		$value = '';

		/**
		 * @var callable $fn
		 */
		$fn = $is_default ? 'pll_default_language' : 'pll_current_language';

		if ( function_exists( $fn ) ) {
			$value = $fn();
		}

		return is_string( $value ) ? $value : '';
	}

	public function get_default_language_object( $queried_object ) {
		if ( $queried_object instanceof WP_Term ) {
			$default_queried_object = $this->map_default_language_object(
				function () use ( $queried_object ) {
					return pll_get_term_translations( $queried_object->term_id );
				},
				function ( int $default_id ) use ( $queried_object ) {
					return get_term( $default_id, $queried_object->taxonomy );
				}
			);
		} else if ( $queried_object instanceof WP_Post ) {
			$default_queried_object = $this->map_default_language_object(
				function () use ( $queried_object ) {
					return pll_get_post_translations( $queried_object->ID );
				},
				function ( int $default_id ) use ( $queried_object ) {
					return get_post( $default_id, $queried_object->post_type );
				}
			);
		}

		return $default_queried_object ?? $queried_object;
	}

	public function is_supported(): bool {
		return function_exists( 'pll_default_language' ) &&
		       function_exists( 'pll_current_language' ) &&
		       function_exists( 'pll_get_post_translations' ) &&
		       function_exists( 'pll_get_term_translations' );
	}

	/**
	 * Maps a given object to its equivalent object in the default language, based on its ID.
	 *
	 * @param callable $language_code_to_id_mapper_fn A function that returns an array mapping language codes to object IDs.
	 * @param callable $object_mapper_fn A function that retrieves the actual object in the default language.
	 *
	 * @return mixed|null The equivalent object in the default language, or null if it cannot be determined.
	 */
	private function map_default_language_object( callable $language_code_to_id_mapper_fn, callable $object_mapper_fn ) {
		if ( ! $this->is_supported() ) {
			return null;
		}

		$default_language_code = $this->get_language_code( true );

		if ( ! $default_language_code ) {
			return null;
		}

		$language_code_to_id = $language_code_to_id_mapper_fn();
		$default_id          = $language_code_to_id[ $default_language_code ] ?? 0;

		if ( $default_id ) {
			$queried_object = $object_mapper_fn( $default_id );
		}

		return $queried_object ?? null;
	}
}
