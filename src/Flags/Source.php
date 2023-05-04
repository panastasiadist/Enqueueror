<?php

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class providing values about the supported ways for an asset to be loaded.
 */
class Source extends Flag {
	const VALUE_INTERNAL = 'internal';
	const VALUE_EXTERNAL = 'external';

	protected static $name = 'source';
	protected static $default_value = Source::VALUE_EXTERNAL;
	protected static $supported_values = array( Source::VALUE_INTERNAL, Source::VALUE_EXTERNAL );
}