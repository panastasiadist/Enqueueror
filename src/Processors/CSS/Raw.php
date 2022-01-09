<?php 

namespace panastasiadist\Enqueueror\Processors\CSS;

use panastasiadist\Enqueueror\Base\Processor;

/**
 * Processor supporting .css files for stylesheets.
 */
class Raw extends Processor
{
    /**
     * Returns the type of assets this processor supports. Supported asset type: 'stylesheets'.
     * 
     * @return string Array containing the string 'stylesheets'.
     */
    public static function get_supported_asset_type()
    {
        return 'stylesheets';
    }

    /**
     * Returns the file extensions this processor supports. Supported extensions: 'css'.
     * 
     * @return string[] Array containing the string 'css'.
     */
    public static function get_supported_extensions()
    {
        return array( 'css' );
    }
}