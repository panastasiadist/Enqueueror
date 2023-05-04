<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Flags\Source;
use panastasiadist\Enqueueror\Flags\Source as SourceFlag;
use panastasiadist\Enqueueror\Flags\Location as LocationFlag;

class Manager {
	private const OUTPUT_RULES = array(
		'scripts'     => array(
			array(
				'location'    => LocationFlag::VALUE_HEAD,
				'source'      => SourceFlag::VALUE_EXTERNAL,
				'output_mode' => 'enqueue'
			),
			array(
				'location'    => LocationFlag::VALUE_HEAD,
				'source'      => SourceFlag::VALUE_INTERNAL,
				'output_mode' => 'print'
			),
			array(
				'location'    => LocationFlag::VALUE_FOOTER,
				'source'      => SourceFlag::VALUE_EXTERNAL,
				'output_mode' => 'enqueue'
			),
			array(
				'location'    => LocationFlag::VALUE_FOOTER,
				'source'      => SourceFlag::VALUE_INTERNAL,
				'output_mode' => 'print'
			),
		),
		'stylesheets' => array(
			array(
				'location'    => LocationFlag::VALUE_HEAD,
				'source'      => SourceFlag::VALUE_EXTERNAL,
				'output_mode' => 'enqueue'
			),
			array(
				'location'    => LocationFlag::VALUE_HEAD,
				'source'      => SourceFlag::VALUE_INTERNAL,
				'output_mode' => 'print'
			),
		),
	);

	/**
	 * @param Asset[] $assets An array of Asset instances to filter according to the rest of the arguments.
	 * @param string $for_location Return only assets meant to be used in the provided location.
	 * @param string[] $output_modes Return only assets whose output mode is compatible with the provided ones.
	 *
	 * @return Asset[] The filtered assets.
	 */
	public static function get_assets_filtered( array $assets, string $for_location, array $output_modes ): array {
		return array_filter( $assets, function ( $asset ) use ( $for_location, $output_modes ) {
			$type     = $asset->get_type();
			$source   = $asset->get_flag( SourceFlag::get_name() );
			$location = $asset->get_flag( LocationFlag::get_name() );

			// Filter out this asset if its designated location does not match the requested one.
			if ( $for_location != $location ) {
				return false;
			}

			$rules = self::OUTPUT_RULES[ $type ] ?? array();

			foreach ( $rules as $rule ) {
				if ( $rule['source'] === $source && $rule['location'] === $location ) {
					if ( in_array( $rule['output_mode'], $output_modes ) ) {
						return true;
					}
				}
			}

			return false;
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