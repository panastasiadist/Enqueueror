<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror\Base;

/**
 * Base class for classes implementing flag functionality.
 * A flag represents instructions an asset provides, regarding how it should be used / processed.
 * A flag may take several values. However, each value is globally unique.
 * In other words, a flag's value is unique to it and not applicable to any other flag.
 */
class Flag {
	protected static $name = '';
	protected static $supported_values = array();

	/**
	 * Returns the flag's name as specified by the overriding class.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return static::$name;
	}

	/**
	 * Returns a flag value supported by this flag if contained in the provided values. If more than one supported flag
	 * values are found in the provided values, only the last encountered will be returned.
	 *
	 * @param string[] $values The values to check against.
	 * @param string $default An optional default value to be returned if a value supported by the flag is not found in
	 * the provided array of values.
	 *
	 * @return string The found flag value or the default value if none flag value has been found.
	 */
	public static function get_detected_value( array $values, string $default = '' ): string {
		$common = array_intersect( $values, static::$supported_values );

		return ( ! empty( $common ) ) ? array_pop( $common ) : $default;
	}
}