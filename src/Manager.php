<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Flags\Source;
use panastasiadist\Enqueueror\Flags\Source as SourceFlag;
use panastasiadist\Enqueueror\Flags\Location as LocationFlag;

class Manager {
	/**
	 * @param Asset[] $assets An array of Asset instances to filter according to the rest of the arguments.
	 * @param string $for_location Return only assets meant to be used in the provided location.
	 * @param string[] $with_sources Return only assets with the provided sources.
	 *
	 * @return Asset[] The filtered assets.
	 */
	public static function get_assets_filtered( array $assets, string $for_location, array $with_sources ): array {
		return array_filter( $assets, function ( $asset ) use ( $for_location, $with_sources ) {
			$source   = $asset->get_flag( SourceFlag::get_name() );
			$location = $asset->get_flag( LocationFlag::get_name() );

			return $for_location === $location && in_array( $source, $with_sources );
		} );
	}

	/**
	 * Returns the supplied assets in the following order:
	 * 1. Language agnostic, global assets (langcode = 'all', type = 'global').
	 * 2. Language agnostic, current object based assets (langcode = 'all', type = 'current').
	 * 3. Current language, global assets (langcode = current language code, type = 'global').
	 * 4. Current language, current object based assets (langcode = current language code, type = 'current').
	 *
	 * @param Asset[] $assets An array of Asset instances to return ordered.
	 *
	 * @return Asset[] The ordered array of Asset instances.
	 */
	public static function get_assets_sorted( array $assets ): array {
		// Assets must be already sorted in ascending order, by directory depth and by file name.
		// So, we only have to group the assets by context and language.

		$global_assets           = array();
		$global_assets_language  = array();
		$current_assets          = array();
		$current_assets_language = array();

		foreach ( $assets as $asset ) {
			if ( 'global' === $asset->get_context() ) {
				if ( 'all' === $asset->get_language_code() ) {
					$global_assets[] = $asset;
				} else {
					$global_assets_language[] = $asset;
				}
			} else {
				if ( 'all' === $asset->get_language_code() ) {
					$current_assets[] = $asset;
				} else {
					$current_assets_language[] = $asset;
				}
			}
		}

		return array_merge( $global_assets, $global_assets_language, $current_assets, $current_assets_language );
	}
}