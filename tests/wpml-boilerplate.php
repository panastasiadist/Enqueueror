<?php

trait WPML_Boilerplate {
	/**
	 * Holds the code of the language that should act as the default one during the testing session.
	 * It becomes null to emulate absence of WPML.
	 *
	 * @var string|null
	 */
	private $wpml_default_language = null;

	/**
	 * Holds the code of the language that should act as the current one during the testing session.
	 * It becomes null to emulate absence of WPML.
	 *
	 * @var string|null
	 */
	private $wpml_current_language = null;

	/**
	 * Holds the ID of a post that should act as a default language's post during the testing session.
	 * It becomes null to emulate absence of WPML.
	 *
	 * @var int|null
	 */
	private $wpml_default_language_post_id = null;

	/**
	 * Holds the ID of a term that should act as a default language's term during the testing session.
	 * It becomes null to emulate absence of WPML.
	 *
	 * @var int|null
	 */
	private $wpml_default_language_term_id = null;

	/**
	 * Holds the ID of a post that should act as a non-default language's post during the testing session.
	 * It becomes null to emulate absence of WPML.
	 *
	 * @var int|null
	 */
	private $wpml_alt_language_post_id = null;

	/**
	 * Holds the ID of a term that should act as a non-default language's term during the testing session.
	 * It becomes null to emulate absence of WPML.
	 *
	 * @var int|null
	 */
	private $wpml_alt_language_term_id = null;

	/**
	 * Set up WPML-related testing stuff.
	 *
	 * @return void
	 */
	private function setUpWPML(): void {
		$this->wpml_default_language = 'en';
		$this->wpml_current_language = 'en';

		add_filter( 'wpml_default_language', array( $this, 'filter_wpml_default_language' ) );
		add_filter( 'wpml_current_language', array( $this, 'filter_wpml_current_language' ) );
		add_filter( 'wpml_object_id', array( $this, 'filter_wpml_object_id' ), 10, 4 );
	}

	/**
	 * Undo WPML-related testing stuff.
	 *
	 * @return void
	 */
	private function tearDownWPML(): void {
		$this->wpml_default_language = null;
		$this->wpml_current_language = null;

		remove_filter( 'wpml_default_language', array( $this, 'filter_wpml_default_language' ) );
		remove_filter( 'wpml_current_language', array( $this, 'filter_wpml_current_language' ) );
		remove_filter( 'wpml_object_id', array( $this, 'filter_wpml_object_id' ), 10, 4 );
	}

	/**
	 * Mocks 'wpml_default_language' filter supported by WPML and used by the code.
	 * Returns the language code of the default language in a WPML supported WordPress installation.
	 *
	 * @param $arg
	 *
	 * @return string|null
	 */
	public function filter_wpml_default_language( $arg ): ?string {
		return $this->wpml_default_language;
	}

	/**
	 * Mocks 'wpml_current_language' filter supported by WPML and used by the code.
	 * Returns the language code of the active language in a WPML supported WordPress installation.
	 *
	 * @param $arg
	 *
	 * @return string|null
	 */
	public function filter_wpml_current_language( $arg ): ?string {
		return $this->wpml_current_language;
	}

	/**
	 * Mocks 'wpml_object_id' filter supported by WPML and used by the code.
	 * Returns the ID corresponding to the language version specified for supplied content ID.
	 *
	 * @param int $element_id
	 * @param string $element_type
	 * @param bool $return_original_if_missing
	 * @param $language_code
	 *
	 * @return int|null
	 */
	public function filter_wpml_object_id( int $element_id, string $element_type, bool $return_original_if_missing = false, $language_code = null ): ?int {
		if ( null == $language_code ) {
			return $element_id;
		}

		if ( 'post' == $element_type ) {
			if ( $language_code == $this->wpml_default_language && $element_id != $this->wpml_default_language_post_id ) {
				return $this->wpml_default_language_post_id;
			} else if ( $language_code != $this->wpml_default_language && $element_id == $this->wpml_default_language_post_id ) {
				return $this->wpml_alt_language_post_id;
			}
		} else if ( 'category' == $element_type ) {
			if ( $language_code == $this->wpml_default_language && $element_id != $this->wpml_default_language_term_id ) {
				return $this->wpml_default_language_term_id;
			} else if ( $language_code != $this->wpml_default_language && $element_id == $this->wpml_default_language_term_id ) {
				return $this->wpml_alt_language_term_id;
			}
		}

		return $element_id;
	}
}
