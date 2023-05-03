<?php

namespace panastasiadist\Enqueueror\Processors\JS;

use panastasiadist\Enqueueror\Base\Processor;

/**
 * Processor supporting .css.php files for stylesheets.
 */
class Php extends Processor {
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
	 * @return string[] Array containing the string 'js.php'.
	 */
	public static function get_supported_extensions(): array {
		return array( 'js.php' );
	}

	/**
	 * Returns the absolute filesystem path to an asset's PHP processed code file.
	 *
	 * @param string $asset_file_path The absolute filesystem path to an asset file to process.
	 *
	 * @return string|false Absolute filesystem path to the provided asset's processed code file. False on failure.
	 */
	public static function get_processed_filepath( string $asset_file_path ) {
		if ( is_readable( $asset_file_path ) ) {
			$file_path = tempnam( sys_get_temp_dir(), 'enq' );

			if ( false !== $file_path ) {
				ob_start();
				require_once( $asset_file_path );
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