<?php 

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class regarding the way an asset should be loaded.
 * Flag name: source
 * Supported flag values: internal, external.
 */
class Source extends Flag
{
    protected static $name = 'source';
    protected static $supported_values = array( 'internal', 'external' );
}