<?php

namespace panastasiadist\Enqueueror\Flags;

use panastasiadist\Enqueueror\Base\Flag;

/**
 * Flag class providing values regarding the supported loading strategies of an asset.
 */
class Loading extends Flag {
	const VALUE_ASYNC = 'async';
	const VALUE_DEFER = 'defer';
	const VALUE_NONE = '';

	protected static $name = 'loading';
	protected static $default_value = Loading::VALUE_NONE;
	protected static $supported_values = array( Loading::VALUE_ASYNC, Loading::VALUE_DEFER, Loading::VALUE_NONE );
}
