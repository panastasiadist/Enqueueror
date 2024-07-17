<?php

use panastasiadist\Enqueueror\Support\Language\WPMLMediator;

class TestWPMLMediator extends WP_UnitTestCase {
	use WPML_Boilerplate;

	public function setUp(): void {
		$this->setUpWPML();
	}

	public function tearDown(): void {
		$this->tearDownWPML();
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
