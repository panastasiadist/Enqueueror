<?php

namespace panastasiadist\Enqueueror\Descriptors;

use Exception;
use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;

class User extends Descriptor
{
	/**
	 * Returns an array of Description instances representing assets that should be loaded when the request is about a
	 * user archive page.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public static function get(): array
	{
		$queried_object = get_queried_object();

		if ( ! $queried_object instanceof \WP_User ) {
			return array();
		}

		return array(
			new Description( 'user' ),
			new Description( 'user-id-' . $queried_object->data->ID ),
		);
	}
}