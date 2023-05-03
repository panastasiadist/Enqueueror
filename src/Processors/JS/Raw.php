<?php

namespace panastasiadist\Enqueueror\Processors\JS;

use panastasiadist\Enqueueror\Base\Processor;

/**
 * Processor supporting .css files for stylesheets.
 */
class Raw extends Processor {
	/**
	 * Returns the asset type supported by the processor.
	 *
	 * @return string Returns 'scripts'.
	 */
	public static function get_supported_asset_type(): string {
		return 'scripts';
	}

	/**
	 * Returns the asset file extensions supported by the processor.
	 *
	 * @return string[] Array containing the string 'js'.
	 */
	public static function get_supported_extensions(): array {
		return array( 'js' );
	}
}