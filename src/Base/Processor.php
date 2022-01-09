<?php 

namespace panastasiadist\Enqueueror\Base;

use panastasiadist\Enqueueror\Base\Asset;

/**
 * Base class for asset processors.
 * An asset processor supports specific asset types and file extensions.
 * It is designed to read a supported asset's file, (optionally) process it, 
 * ultimately returning an absolute file path to a file ready for consumption.
 */
class Processor
{
    /**
     * Should be overriden to return an array of asset file extensions supported by the processor.
     */
    public static function get_supported_extensions()
    {
        return array();
    }

    /**
     * Should be overriden to return the asset type supported by the processor.
     */
    public static function get_supported_asset_type()
    {
        return '';
    }

    /**
     * Returns the absolute filesystem path to an asset's file.
     * 
     * @param Asset $asset An instance representing a file asset to process.
     * @return string|false Absolute filesystem path to the passed-in asset's file. False on failure.
     */
    public static function get_processed_filepath( Asset $asset )
    {
        if ( is_readable( $asset->get_filepath() ) ) {
            return $asset->get_filepath();
        }

        return false;
    }
}