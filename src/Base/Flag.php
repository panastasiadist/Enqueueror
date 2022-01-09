<?php 
declare(strict_types=1);

namespace panastasiadist\Enqueueror\Base;

use panastasiadist\Enqueueror\Base\Asset;

/**
 * Base class for classes implementing flag functionality.
 * A flag represents instructions an asset provides, regarding how it should be used / processed.
 * A flag may take several values. However each value is globally unique.
 * In other words, a flag's value is unique to it and not applicable to any other flag.
 */
class Flag
{
    protected static $name = '';
    protected static $supported_values = array();

    /**
     * [Should be reimplemented] Returns the flag's name as specified by the overriding class.
     * 
     * @return string
     */
    public static function get_name()
    {
        return static::$name;
    }

    /**
     * Returns a flag value supported by this flag if contained in the passed-in values. If more than one supported flag 
     * values are found in the passed-in values, only the first encountered will be returned.
     * 
     * @param array[] $values The values to check against.
     * @return string|boolean The found flag value or false if none flag value has been found.
     */
    public static function get_detected_value( array $values )
    {
        $common = array_intersect( $values, static::$supported_values );
        return ( ! empty( $common ) ) ? array_pop( $common ) : false;
    }

    /**
     * Returns the flag supported value if found in the passed-in asset.
     * 
     * @param Asset $asset The asset to check against.
     * @param string $default An optional default value to be returned if a flag supported value is not found in the 
     * passed-in asset.
     * @return string The found flag value or default value.
     */
    public static function get_value_for_asset( Asset $asset, string $default = '' )
    {
        $value = static::get_detected_value( $asset->get_flags() );
        return false !== $value ? $value : $default;
    }

    /**
     * Returns whether the passed-in asset supports the specified flag value. The flag value is not required to be 
     * supported by the flag.
     * 
     * @param Asset $asset The asset to check.
     * @param string $value The flag value to check.
     * @return boolean True if the passed-in flag value is supported by the passed-in asset.
     */
    public static function asset_supports_flag_value( Asset $asset, string $value )
    {
        return in_array( $value, $asset->get_flags() );
    }
}