<?php 

namespace panastasiadist\Enqueueror\Processors\JS;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Base\Processor;

/**
 * Processor supporting .css.php files for stylesheets.
 */
class Php extends Processor
{
    /**
     * Returns the type of assets this processor supports. Supported asset type: 'stylesheets'.
     * 
     * @return string Array containing the string 'stylesheets'.
     */
    public static function get_supported_asset_type()
    {
        return 'scripts';
    }

    /**
     * Returns the file extensions this processor supports. Supported extensions: 'css.php'.
     * 
     * @return string[] Array containing the string 'css.php'.
     */
    public static function get_supported_extensions()
    {
        return array( 'js.php' );
    }

    /**
     * Returns the absolute filesystem path to an asset's PHP processed code file.
     * 
     * @param Asset $asset An instance representing a file asset to process.
     * @return string|false Absolute filesystem path to the passed-in asset's processed code file. False on failure.
     */
    public static function get_processed_filepath( Asset $asset )
    {
        if ( is_readable( $asset->get_filepath() ) ) {
            $file_path = tempnam( sys_get_temp_dir(), 'infse' );

            if ( false !== $file_path ) {
                ob_start();
                require_once( $asset->get_filepath() );
                $buffer = ob_get_clean();

                $buffer = preg_replace( '/<\/?script.*>/', '', $buffer );
                
                if ( file_put_contents( $file_path, $buffer ) ) {
                    return $file_path;
                }
            }
        }

        return false;
    }
}