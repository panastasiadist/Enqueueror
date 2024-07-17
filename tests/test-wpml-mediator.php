<?php

use panastasiadist\Enqueueror\Support\Language\WPMLMediator;

class TestWPMLMediator extends WP_UnitTestCase {
	use WPML_Boilerplate;

	private $wpml_default_language_post_id = null;
	private $wpml_default_language_term_id = null;
	private $wpml_alt_language_post_id = null;
	private $wpml_alt_language_term_id = null;

	public function setUp(): void {
		$this->wpml_default_language = 'en';
		$this->wpml_current_language = 'en';

		add_filter( 'wpml_default_language', array( $this, 'filter_wpml_default_language' ) );
		add_filter( 'wpml_current_language', array( $this, 'filter_wpml_current_language' ) );
		add_filter( 'wpml_object_id', array( $this, 'filter_wpml_object_id' ), 10, 4 );
	}

	public function tearDown(): void {
		$this->wpml_default_language = null;
		$this->wpml_current_language = null;

		remove_filter( 'wpml_default_language', array( $this, 'filter_wpml_default_language' ) );
		remove_filter( 'wpml_current_language', array( $this, 'filter_wpml_current_language' ) );
		remove_filter( 'wpml_object_id', array( $this, 'filter_wpml_object_id' ), 10, 4 );
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
	public function filter_wpml_object_id( int $element_id, string $element_type, bool $return_original_if_missing = false, $language_code = null ) {
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

	public function test_wpml_is_detected() {
		define( 'ICL_SITEPRESS_VERSION', true );
		$this->assertTrue( ( new WPMLMediator() )->is_supported() );
	}

	public function test_wpml_language_getters() {
		$mediator = new WPMLMediator();

		foreach ( array( array( 'en', 'el' ), array( 'el', 'en' ) ) as $package ) {
			$this->wpml_default_language = $package[0];
			$this->wpml_current_language = $package[1];

			$this->assertEquals( $this->wpml_default_language, $mediator->get_language_code( true ), 'The default language code is returned' );
			$this->assertEquals( $this->wpml_current_language, $mediator->get_language_code( false ), 'The current language code is returned' );
		}
	}

	public function test_wpml_default_language_mapping_for_posts() {
		$mediator     = new WPMLMediator();
		$post_default = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );
		$post_alt     = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );

		$this->wpml_default_language_post_id = $post_default->ID;
		$this->wpml_alt_language_post_id     = $post_alt->ID;

		$this->assertEquals( $post_default->ID, $mediator->get_default_language_object( $post_default )->ID, "The default language's post is returned when the default language's post is supplied" );
		$this->assertEquals( $post_default->ID, $mediator->get_default_language_object( $post_alt )->ID, "The default language's post is returned when a non default language's post is supplied" );
	}

	public function test_wpml_default_language_mapping_for_terms() {
		$mediator     = new WPMLMediator();
		$term_default = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );
		$term_alt     = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );

		$this->wpml_default_language_term_id = $term_default->term_id;
		$this->wpml_alt_language_term_id     = $term_alt->term_id;

		$this->assertEquals( $term_default->term_id, $mediator->get_default_language_object( $term_default )->term_id, "The default language's term is returned when the default language's term is supplied" );
		$this->assertEquals( $term_default->term_id, $mediator->get_default_language_object( $term_default )->term_id, "The default language's term is returned when a non default language's term is supplied" );
	}
}
