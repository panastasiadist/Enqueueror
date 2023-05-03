<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror\Base;

use InvalidArgumentException;

/**
 * Represents information about an asset file which is candidate for processing.
 */
class Description {
	private $pattern;
	private $context;
	private $language_code;

	/**
	 * @param string $pattern The asset's name pattern.
	 * @param string $context The asset's target. Valid values are 'global' or 'current'.
	 * @param string $language_code The language's code targeted by the asset's name.
	 * Valid values are 'all' or a language code
	 */
	public function __construct( string $pattern, string $context = 'current', string $language_code = 'all' ) {
		if ( 'current' !== $context && 'global' !== $context ) {
			throw new InvalidArgumentException( "Invalid context '$context' provided" );
		}

		if ( '' === $language_code ) {
			throw new InvalidArgumentException( "No language code provided" );
		}

		$this->pattern       = $pattern;
		$this->context       = $context;
		$this->language_code = $language_code;
	}

	/**
	 * Returns the regex pattern matching the name of assets represented by this rule.
	 *
	 * @return string
	 */
	public function get_pattern(): string {
		return $this->pattern;
	}

	/**
	 * Return the context (global or current) supported by assets represented by this rule.
	 *
	 * @return string Returns 'global' or 'current'.
	 */
	public function get_context(): string {
		return $this->context;
	}

	/**
	 * Returns the code of the language supported by assets represented by this rule.
	 *
	 * @return string
	 */
	public function get_language_code(): string {
		return $this->language_code;
	}
}