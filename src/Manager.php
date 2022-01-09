<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Flags\Source as SourceFlag;
use panastasiadist\Enqueueror\Flags\Location as LocationFlag;

class Manager
{
    private const OUTPUT_RULES = array(
        'scripts' => array(
            array( 'location' => 'head', 'source' => 'external', 'output_mode' => 'enqueue' ),
            array( 'location' => 'head', 'source' => 'internal', 'output_mode' => 'print' ),
            array( 'location' => 'footer', 'source' => 'external', 'output_mode' => 'enqueue' ),
            array( 'location' => 'footer', 'source' => 'internal', 'output_mode' => 'print' ),
        ),
        'stylesheets' => array(
            array( 'location' => 'head', 'source' => 'external', 'output_mode' => 'enqueue' ),
            array( 'location' => 'head', 'source' => 'internal', 'output_mode' => 'print' ),
        ),
    );

    public static function get_assets_filtered( array $assets, string $for_location, array $output_modes )
    {
        $rules = self::OUTPUT_RULES;

        $assets = array_filter($assets, function( $asset ) use ( $for_location, $output_modes, $rules ) {
            $type = $asset->get_type();
            $source = SourceFlag::get_value_for_asset( $asset, 'external' );
            $location = LocationFlag::get_value_for_asset( $asset, 'head' );

            // Filter out this asset if its designated location does not match the requested one.
            if ( $for_location != $location ) {
                return false;
            }

            // Filter out this asset if its type is not supported.
            if ( ! isset( $rules[ $type ] ) ) {
                return false;
            }
            
            foreach ( $rules[ $type ] as $rule ) {
                if ( $rule[ 'source' ] === $source && $rule[ 'location' ] === $location ) {
                    if ( in_array( $rule[ 'output_mode' ], $output_modes ) ) {
                        return true;
                    }
                }
            }
            
            return false;
        });

        return $assets;
    }

    /**
     * Returns the supplied assets in the following order: 
     * 1. Language agnostic, global assets (langcode = 'all', type = 'global'). 
     * 2. Language agnostic, current object based assets (langcode = 'all', type = 'current'). 
     * 3. Current language, global assets (langcode = current language code, type = 'global'). 
     * 4. Current language, current object based assets (langcode = current language code, type = 'current'). 
     *
     * @param Asset[] $assets An array of Asset instances to return ordered.
     * @return Asset[] The ordered array of Asset instances.
     */
    public static function get_assets_sorted( array $assets )
    {
        // Assets must be already sorted in ascending order, by directory depth and by file name.
        // So, we only have to sort the assets by type.

        $global_assets = array_filter($assets, function( $asset ) {
            return 'global' === $asset->get_context();
        });

        usort($global_assets, function( $a, $b ) {
            return $a->get_langcode() === $b->get_langcode() ? 0 : ( 'all' === $a->get_langcode() ? -1 : 1 );
        });

        $current_assets = array_filter($assets, function( $asset ) {
            return 'current' === $asset->get_context();
        });

        usort($current_assets, function( $a, $b ) {
            return $a->get_langcode() === $b->get_langcode() ? 0 : ( 'all' === $a->get_langcode() ? -1 : 1 );
        });
        
        return array_merge( $global_assets, $current_assets );
    }
}