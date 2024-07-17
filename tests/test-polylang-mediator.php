<?php

$polylang_default_language        = null;
$polylang_current_language        = null;
$polylang_post_id_to_translations = array();
$polylang_term_id_to_translations = array();

/**
 * Mocks the respective Polylang function. Returns the code of the default language.
 *
 * @return string|null
 */
function pll_default_language(): ?string {
	global $polylang_default_language;

	return $polylang_default_language;
}

/**
 * Mocks the respective Polylang function. Returns the code of the current language.
 *
 * @return string|null
 */
function pll_current_language(): ?string {
	global $polylang_current_language;

	return $polylang_current_language;
}

/**
 * Mocks the respective Polylang function. Returns the IDs of the translated posts for a given post.
 *
 * @param int $post_id The ID of the post to retrieve translations for.
 *
 * @return array<string,int> An associative array of translations, where the keys are the language
 *               codes and the values are the corresponding post IDs for each translation.
 */
function pll_get_post_translations( int $post_id ): array {
	global $polylang_post_id_to_translations;

	return $polylang_post_id_to_translations[ $post_id ] ?? array();
}

/**
 * Mocks the respective Polylang function. Returns the IDs of the translated terms for a given post.
 *
 * @param int $term_id The ID of the term to retrieve translations for.
 *
 * @return array<string,int> An associative array of translations, where the keys are the language
 *               codes and the values are the corresponding term IDs for each translation.
 */
function pll_get_term_translations( int $term_id ): array {
	global $polylang_term_id_to_translations;

	return $polylang_term_id_to_translations[ $term_id ] ?? array();
}

use panastasiadist\Enqueueror\Support\Language\PolylangMediator;

class TestPolylangMediator extends WP_UnitTestCase {
	public function setUp(): void {
		global $polylang_default_language,
		       $polylang_current_language,
		       $polylang_post_id_to_translations,
		       $polylang_term_id_to_translations;

		$polylang_default_language        = 'en';
		$polylang_current_language        = 'en';
		$polylang_post_id_to_translations = array();
		$polylang_term_id_to_translations = array();
	}

	public function tearDown(): void {
		global $polylang_default_language,
		       $polylang_current_language,
		       $polylang_post_id_to_translations,
		       $polylang_term_id_to_translations;

		$polylang_default_language        = null;
		$polylang_current_language        = null;
		$polylang_post_id_to_translations = array();
		$polylang_term_id_to_translations = array();
	}

	public function test_is_detected() {
		$this->assertTrue( ( new PolylangMediator() )->is_supported() );
	}

	public function test_language_getters() {
		global $polylang_default_language, $polylang_current_language;

		$mediator = new PolylangMediator();

		foreach ( array( array( 'en', 'el' ), array( 'el', 'en' ) ) as $package ) {
			$polylang_default_language = $package[0];
			$polylang_current_language = $package[1];

			$this->assertEquals( $polylang_default_language, $mediator->get_language_code( true ), 'The default language code is returned' );
			$this->assertEquals( $polylang_current_language, $mediator->get_language_code( false ), 'The current language code is returned' );
		}
	}

	public function test_default_language_mapping_for_posts() {
		global $polylang_default_language, $polylang_current_language, $polylang_post_id_to_translations;

		$mediator     = new PolylangMediator();
		$post_default = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );
		$post_alt     = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );

		$polylang_default_language = 'en';
		$polylang_current_language = 'el';

		foreach ( array( $post_default, $post_alt ) as $post_object ) {
			$polylang_post_id_to_translations[ $post_object->ID ] = array(
				$polylang_default_language => $post_default->ID,
				$polylang_current_language => $post_alt->ID,
			);
		}

		$this->assertEquals( $post_default->ID, $mediator->get_default_language_object( $post_default )->ID, "The default language's post is returned when the default language's post is supplied" );
		$this->assertEquals( $post_default->ID, $mediator->get_default_language_object( $post_alt )->ID, "The default language's post is returned when a non default language's post is supplied" );
	}

	public function test_default_language_mapping_for_terms() {
		global $polylang_default_language, $polylang_current_language, $polylang_term_id_to_translations;

		$mediator     = new PolylangMediator();
		$term_default = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );
		$term_alt     = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );

		$polylang_default_language = 'en';
		$polylang_current_language = 'el';

		foreach ( array( $term_default, $term_alt ) as $term_object ) {
			$polylang_term_id_to_translations[ $term_object->term_id ] = array(
				$polylang_default_language => $term_default->term_id,
				$polylang_current_language => $term_alt->term_id,
			);
		}

		$this->assertEquals( $term_default->term_id, $mediator->get_default_language_object( $term_default )->term_id, "The default language's term is returned when the default language's term is supplied" );
		$this->assertEquals( $term_default->term_id, $mediator->get_default_language_object( $term_alt )->term_id, "The default language's term is returned when a non default language's term is supplied" );
	}
}
