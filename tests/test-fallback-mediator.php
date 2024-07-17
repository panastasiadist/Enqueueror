<?php

use panastasiadist\Enqueueror\Support\Language\FallbackMediator;

class TestFallbackMediator extends WP_UnitTestCase {
	public function test_is_detected() {
		$this->assertTrue( ( new FallbackMediator() )->is_supported() );
	}

	public function test_language_getters() {
		$mediator = new FallbackMediator();

		$this->assertEquals( '', $mediator->get_language_code( true ) );
		$this->assertEquals( '', $mediator->get_language_code( false ) );
	}

	public function test_default_language_mapping_for_posts() {
		$mediator = new FallbackMediator();
		$post     = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );

		$this->assertEquals( $post->ID, $mediator->get_default_language_object( $post )->ID, 'The same post instance is always returned' );
	}

	public function test_default_language_mapping_for_terms() {
		$mediator = new FallbackMediator();
		$term     = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );

		$this->assertEquals( $term->term_id, $mediator->get_default_language_object( $term )->term_id, 'The same post instance is always returned' );
	}
}
