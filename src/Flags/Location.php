<?php

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class providing values about the supported locations an asset may be used at.
 */
class Location extends Flag {
	const VALUE_HEAD = 'head';
	const VALUE_FOOTER = 'footer';

	protected static $name = 'location';
	protected static $default_value = Location::VALUE_HEAD;
	protected static $supported_values = array( Location::VALUE_HEAD, Location::VALUE_FOOTER );
}