<?php

namespace panastasiadist\Enqueueror\Base;

/**
 * Base class for asset processors.
 * An asset processor supports specific asset types and file extensions.
 * It reads & process a supported asset's file, returning an absolute path to the (processed) file.
 */
class Processor {
	/**
	 * Override to return an array of asset file extensions supported by the processor.
	 *
	 * @return string[] Array of extensions supported by the processor.
	 */
	public static function get_supported_extensions(): array {
		return array();
	}

	/**
	 * Override to return the asset type supported by the processor.
	 *
	 * @return string The asset type supported by the processor.
	 */
	public static function get_supported_asset_type(): string {
		return '';
	}

	/**
	 * Returns the absolute filesystem path to an asset's file.
	 *
	 * @param string $asset_file_path The absolute filesystem path to an asset file to process.
	 *
	 * @return string|false Absolute filesystem path to the provided asset's file. False on failure.
	 */
	public static function get_processed_filepath( string $asset_file_path ) {
		if ( is_readable( $asset_file_path ) ) {
			return $asset_file_path;
		}

		return false;
	}

	/**
	 * Returns the header values found in the provided asset file.
	 *
	 * @param string $asset_file_path An Asset instance of an asset file to return its header values.
	 *
	 * @return array|bool An associative array containing directive => value pairs as found in the asset's file header.
	 * Empty if no header section or no header values are specified in the asset's file.
	 * False if unable to read the contents of the provided file path.
	 */
	public static function get_header_values( string $asset_file_path ) {
		$content = file_get_contents( $asset_file_path );

		if ( false === $content ) {
			return false;
		}

		$fields = array();

		$stop  = false;
		$start = mb_strpos( $content, '/*' );

		if ( false !== $start ) {
			$stop = mb_strpos( $content, '*/', $start + 2 );
		}

		$header = '';

		if ( false !== $start && false !== $stop ) {
			$header = mb_substr( $content, $start + 2, $stop - $start - 2 );
		}

		$lines = explode( PHP_EOL, $header );

		foreach ( $lines as $line ) {
			$matches = array();

			if ( preg_match( '/[*\h\t]*([a-zA-Z]+)[\h\t]*:([^*]+)\**/', $line, $matches ) ) {
				$name            = $matches[1];
				$value           = $matches[2];
				$fields[ $name ] = $value;
			}
		}

		return $fields;
	}
}