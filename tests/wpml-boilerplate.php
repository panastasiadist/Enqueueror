<?php

trait WPML_Boilerplate
{
	private $wpml_default_language = null;
	private $wpml_current_language = null;

	/**
	 * Mocks 'wpml_default_language' filter supported by WPML and used by the code.
	 * Returns the language code of the default language in a WPML supported WordPress installation.
	 *
	 * @param $arg
	 *
	 * @return null
	 */
	public function filter_wpml_default_language( $arg )
	{
		return $this->wpml_default_language;
	}

	/**
	 * Mocks 'wpml_current_language' filter supported by WPML and used by the code.
	 * Returns the language code of the active language in a WPML supported WordPress installation.
	 *
	 * @param $arg
	 *
	 * @return null
	 */
	public function filter_wpml_current_language( $arg )
	{
		return $this->wpml_current_language;
	}
}