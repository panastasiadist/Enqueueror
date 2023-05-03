<?php

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class providing values about the supported ways for an asset to be loaded.
 */
class Source extends Flag {
	protected static $name = 'source';
	protected static $supported_values = array( 'internal', 'external' );
}