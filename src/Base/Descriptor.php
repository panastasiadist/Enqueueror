<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror\Base;

use Exception;
use panastasiadist\Enqueueror\Base\Description;

/**
 * Abstract class for classes providing descriptors.
 */
abstract class Descriptor {
	/**
	 * Instructs the multilingual mechanism (if any) to switch to the language specified by the provided language code.
	 *
	 * @param string $language_code Language code of the language to switch to.
	 *
	 * @return void
	 */
	private static function switch_language( string $language_code ) {
		do_action( 'wpml_switch_language', $language_code );
	}

	/**
	 * Returns the language code of the default language or null if the website is not multilingual.
	 *
	 * @return mixed|null
	 */
	protected static function get_default_language_code() {
		return apply_filters( 'wpml_default_language', null );
	}

	/**
	 * Returns the language code of the active language or null if the website is not multilingual.
	 *
	 * @return mixed|null
	 */
	protected static function get_current_language_code() {
		return apply_filters( 'wpml_current_language', null );
	}

	/**
	 * Returns the provided array of Description instances enriched by new Description instances which correspond to the
	 * active language, provided that the website is multilingual. If the website is not multilingual, the provided
	 * array of Description instances is returned unmodified.
	 *
	 * @param Description[] $descriptions The initial array of Description instances to enrich.
	 *
	 * @return Description[] The enriched array of Description instances.
	 */
	protected static function get_language_enriched_descriptors( array $descriptions ): array {
		$current_language_code = self::get_current_language_code();

		if ( ! $current_language_code ) {
			return $descriptions;
		}

		return array_merge( $descriptions, array_map( function ( $description ) use ( $current_language_code ) {
			return new Description(
				$description->get_pattern() . '-' . $current_language_code,
				$description->get_context(),
				$current_language_code
			);
		}, $descriptions ) );
	}

	/**
	 * Returns the instance corresponding to the default language version of the provided object.
	 * The provided object is returned unmodified if it already corresponds to the default language of the website, or
	 * if the object is not a \WP_Post or a \WP_Term instance, or if the website is not multilingual.
	 *
	 * @param \WP_Post|\WP_Term $queried_object
	 *
	 * @return \WP_Post|\WP_Term
	 */
	protected static function get_default_language_object( $queried_object ) {
		$default_language_code = self::get_default_language_code();
		$current_language_code = self::get_current_language_code();

		if ( ! ( $default_language_code && $current_language_code ) ) {
			return $queried_object;
		}

		$default_language_object = $queried_object;

		if ( $queried_object instanceof \WP_Term ) {
			$default_id = apply_filters( 'wpml_object_id', $queried_object->term_id, $queried_object->taxonomy, true, $default_language_code );

			if ( $default_id !== $queried_object->term_id ) {
				self::switch_language( $default_language_code );
				$default_language_object = get_term( $default_id, $queried_object->taxonomy );
				self::switch_language( $current_language_code );
			}
		} else if ( $queried_object instanceof \WP_Post ) {
			$default_id = apply_filters( 'wpml_object_id', $queried_object->ID, $queried_object->post_type, true, $default_language_code );

			if ( $default_id !== $queried_object->ID ) {
				self::switch_language( $default_language_code );
				$default_language_object = get_post( $default_id, $queried_object->post_type );
				self::switch_language( $current_language_code );
			}
		}

		return $default_language_object;
	}

	/**
	 * Returns an array of Description instances per the logic implemented by the class implementing the method.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public abstract static function get(): array;
}