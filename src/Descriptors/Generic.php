<?php

namespace panastasiadist\Enqueueror\Descriptors;

use Exception;
use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class Generic extends Descriptor
{
	/**
	 * Returns an array of Description instances representing assets that should be loaded irrespective of the content
	 * requested.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public static function get(): array
	{
		return static::get_language_enriched_descriptors(array(
			new Description( 'global', 'global' ),
		));
	}
}