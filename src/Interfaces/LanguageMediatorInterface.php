<?php

namespace panastasiadist\Enqueueror\Interfaces;

use WP_Post;
use WP_Term;

interface LanguageMediatorInterface {
	/**
	 * Returns the default or current language code if the translation handling plugin is active and properly configured.
	 * If the plugin is not set up or the language code is not a string, this function will return an empty string.
	 *
	 * @param bool $is_default - A value indicating whether to return the default or current language code.
	 *
	 * @return string An empty string or the default/current language code.
	 */
	function get_language_code( bool $is_default ): string;

	/**
	 * Returns an instance that corresponds to the default language version of the given object.
	 * However, the same instance will be returned in the following circumstances:
	 * - If the translation handling plugin is not active or properly set up,
	 * - If the input object already matches with the default language of the website,
	 * - If the object instance is neither \WP_Post nor \WP_Term.
	 *
	 * @param WP_Post|WP_Term $queried_object The object that is to be processed.
	 *
	 * @return WP_Post|WP_Term Either the translated version of the object in the default language or the original
	 * object based on the conditions mentioned above.
	 */
	function get_default_language_object( $queried_object );

	/**
	 * Determines whether the mediator is applicable to the current WordPress installation.
	 *
	 * @return bool A boolean value indicating the support status of the mediator in the current WordPress installation.
	 */
	function is_supported(): bool;
}
