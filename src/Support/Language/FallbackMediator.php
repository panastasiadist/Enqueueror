<?php

namespace panastasiadist\Enqueueror\Support\Language;

use WP_Post;
use WP_Term;
use panastasiadist\Enqueueror\Interfaces\LanguageMediatorInterface;

class FallbackMediator implements LanguageMediatorInterface {
	public function get_language_code( bool $is_default ): string {
		return '';
	}

	public function get_default_language_object( $queried_object ) {
		return ( $queried_object instanceof WP_Term || $queried_object instanceof WP_Post ) ? $queried_object : null;
	}

	public function is_supported(): bool {
		return true;
	}
}
