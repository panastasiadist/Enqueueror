<?php 

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class providing values about the supported locations an asset may be used at.
 */
class Location extends Flag
{
    protected static $name = 'location';
    protected static $supported_values = array( 'head', 'footer' );
}