<?php 

namespace panastasiadist\Enqueueror\Processors\CSS;

use panastasiadist\Enqueueror\Base\Processor;

/**
 * Processor supporting .css files for stylesheets.
 */
class Raw extends Processor
{
	/**
	 * Returns the asset type supported by the processor.
	 *
	 * @return string Returns 'stylesheets'.
	 */
    public static function get_supported_asset_type(): string
    {
        return 'stylesheets';
    }

	/**
	 * Returns the asset file extensions supported by the processor.
	 *
	 * @return string[] Array containing the string 'css'.
	 */
    public static function get_supported_extensions(): array
    {
        return array( 'css' );
    }
}