<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror\Base;

use panastasiadist\Enqueueror\Interfaces\LanguageMediatorInterface;

/**
 * Abstract class for classes providing descriptors.
 */
abstract class Descriptor {
	/**
	 * The language mediator used by the Descriptor for language/translation related operations.
	 *
	 * @var LanguageMediatorInterface
	 */
	protected $language_mediator;

	public function __construct( LanguageMediatorInterface $mediator ) {
		$this->language_mediator = $mediator;
	}

	/**
	 * Returns the provided array of Description instances enriched by new Description instances which correspond to the
	 * active language, provided that the website is multilingual. If the website is not multilingual, the provided
	 * array of Description instances is returned unmodified.
	 *
	 * @param Description[] $descriptions The initial array of Description instances to enrich.
	 *
	 * @return Description[] The enriched array of Description instances.
	 */
	protected function get_language_enriched_descriptions( array $descriptions ): array {
		$current_language_code = $this->language_mediator->get_language_code( false );

		if ( ! $current_language_code ) {
			return $descriptions;
		}

		return array_merge( $descriptions, array_map( function ( $description ) use ( $current_language_code ) {
			return new Description(
				$description->get_pattern() . '-' . $current_language_code,
				$description->get_context(),
				$current_language_code
			);
		}, $descriptions ) );
	}

	/**
	 * Returns an array of Description instances per the logic implemented by the class implementing the method.
	 *
	 * @return Description[] An array of Description instances.
	 */
	public abstract function get(): array;
}
