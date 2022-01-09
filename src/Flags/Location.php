<?php 

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class regarding the location an asset should be output.
 * Flag name: location
 * Supported flag values: head, footer.
 */
class Location extends Flag
{
    protected static $name = 'location';
    protected static $supported_values = array( 'head', 'footer' );
}