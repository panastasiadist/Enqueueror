<?php

use panastasiadist\Enqueueror\Base\Descriptor;
use panastasiadist\Enqueueror\Descriptors\Archive;
use panastasiadist\Enqueueror\Descriptors\Generic;
use panastasiadist\Enqueueror\Descriptors\NotFound;
use panastasiadist\Enqueueror\Descriptors\Post;
use panastasiadist\Enqueueror\Descriptors\Search;
use panastasiadist\Enqueueror\Descriptors\Term;
use panastasiadist\Enqueueror\Descriptors\User;
use panastasiadist\Enqueueror\Interfaces\LanguageMediatorInterface;
use panastasiadist\Enqueueror\Support\Language\FallbackMediator;
use panastasiadist\Enqueueror\Support\Language\WPMLMediator;

require_once( __DIR__ . '/wpml-boilerplate.php' );

class TestDescriptors extends WP_UnitTestCase {
	use WPML_Boilerplate;

	private $wpml_default_language_post_id = null;
	private $wpml_default_language_term_id = null;
	private $wpml_alt_language_post_id = null;
	private $wpml_alt_language_term_id = null;

	/**
	 * Uses the supplied Descriptor class to test if a set of Description instances is returned for the supplied
	 * language codes.
	 *
	 * @param array $language_code_to_descriptions_expected An array of Description instances by language code.
	 * @param string $descriptor_class Descriptor class to use.
	 *
	 * @return void
	 */
	private function run_tests( array $language_code_to_descriptions_expected, string $descriptor_class ) {
		foreach ( $language_code_to_descriptions_expected as $language_code => $descriptions_expected ) {
			/**
			 * @var LanguageMediatorInterface $language_mediator
			 */
			$language_mediator = null;

			if ( 'no_multilingual' !== $language_code ) {
				$this->wpml_current_language = $language_code;
				$language_mediator           = new WPMLMediator();
			} else {
				$this->wpml_current_language = null;
				$language_mediator           = new FallbackMediator();
			}

			/**
			 * @var Descriptor $descriptor
			 */
			$descriptor = new $descriptor_class( $language_mediator );

			$descriptions = $descriptor->get();

			foreach ( $descriptions as $description ) {
				$description_actual = 'pattern=' . $description->get_pattern() . '|' .
				                      'context=' . $description->get_context() . '|' .
				                      'language_code=' . $description->get_language_code();

				$this->assertContains( $description_actual, $descriptions_expected );
			}

			$this->assertEquals( count( $descriptions_expected ), count( $descriptions ) );
		}
	}

	/**
	 * Called before the first test method is run.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void {
		register_post_type( 'product', array( 'has_archive' => true ) );
	}

	/**
	 * Runs before a test method is called to prepare the ground for the test to be run.
	 *
	 * @return void
	 */
	public function setUp(): void {
		$this->setUpWPML();
	}

	/**
	 * Runs after a test method has run to clean up stuff.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		$this->tearDownWPML();
	}

	/**
	 * Tests that all relevant Description instances are returned for a post type based archive page.
	 *
	 * @return void
	 */
	public function test_descriptor_archive_post_type() {
		// Navigate to a non-archive url to test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
			'en'              => array(),
			'el'              => array(),
		);

		$this->go_to( get_site_url() );
		$this->run_tests( $language_code_to_descriptions_expected, Archive::class );

		// Go to a non post type based archive url to test that no Description instances are returned.

		$this->go_to( get_day_link( false, false, false ) );
		$this->run_tests( $language_code_to_descriptions_expected, Archive::class );

		// Go to a post type based archive page url to test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=archive|context=current|language_code=all',
				'pattern=archive-type-product|context=current|language_code=all',
			),
			'en'              => array(
				'pattern=archive|context=current|language_code=all',
				'pattern=archive-type-product|context=current|language_code=all',
				'pattern=archive-en|context=current|language_code=en',
				'pattern=archive-type-product-en|context=current|language_code=en',
			),
		);

		$this->go_to( get_post_type_archive_link( 'product' ) );
		$this->run_tests( $language_code_to_descriptions_expected, Archive::class );
	}

	/**
	 * Tests that all relevant Description instances are returned irrespective of the content requested.
	 *
	 * @return void
	 */
	public function test_descriptor_generic() {
		// Don't go to a specific URL to test that all content-agnostic Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=global|context=global|language_code=all',
			),
			'en'              => array(
				'pattern=global|context=global|language_code=all',
				'pattern=global-en|context=global|language_code=en',
			),
		);

		$this->run_tests( $language_code_to_descriptions_expected, Generic::class );
	}

	/**
	 * Tests that all relevant Description instances are returned for the 404 error page.
	 *
	 * @return void
	 */
	public function test_descriptor_not_found() {
		// Go to an existent url to test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
			'en'              => array(),
			'el'              => array(),
		);

		$this->go_to( '/' );
		$this->run_tests( $language_code_to_descriptions_expected, NotFound::class );

		// Go to a non-existent url and test that all Description instances applicable to the 404 page are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=not-found|context=current|language_code=all',
			),
			'en'              => array(
				'pattern=not-found|context=current|language_code=all',
				'pattern=not-found-en|context=current|language_code=en',
			),
			'el'              => array(
				'pattern=not-found|context=current|language_code=all',
				'pattern=not-found-el|context=current|language_code=el',
			),
		);

		$this->go_to( '/?p=-1' );
		$this->run_tests( $language_code_to_descriptions_expected, NotFound::class );
	}

	/**
	 * Tests that all relevant Description instances are returned for a post-type content page.
	 *
	 * @return void
	 */
	public function test_descriptor_post() {
		// Navigate to a non-post url and test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
			'en'              => array(),
			'el'              => array(),
		);

		$this->go_to( '/' );
		$this->run_tests( $language_code_to_descriptions_expected, Post::class );

		// Create dummy posts to simulate language based post Descriptions.

		$post_default = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );
		$post_alt     = $this->factory()->post->create_and_get( array( 'post_type' => 'post' ) );

		$this->wpml_default_language_post_id = $post_default->ID;
		$this->wpml_alt_language_post_id     = $post_alt->ID;

		// Go to a post url in the default language and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=type|context=current|language_code=all',
				'pattern=type-id-' . $post_default->ID . '|context=current|language_code=all',
				'pattern=type-slug-' . $post_default->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '-slug-' . $post_default->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '-id-' . $post_default->ID . '|context=current|language_code=all',
			),
			'en'              => array(
				'pattern=type|context=current|language_code=all',
				'pattern=type-id-' . $post_default->ID . '|context=current|language_code=all',
				'pattern=type-slug-' . $post_default->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '-slug-' . $post_default->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '-id-' . $post_default->ID . '|context=current|language_code=all',
				'pattern=type-en|context=current|language_code=en',
				'pattern=type-id-' . $post_default->ID . '-en|context=current|language_code=en',
				'pattern=type-slug-' . $post_default->post_name . '-en|context=current|language_code=en',
				'pattern=type-' . $post_default->post_type . '-en|context=current|language_code=en',
				'pattern=type-' . $post_default->post_type . '-slug-' . $post_default->post_name . '-en|context=current|language_code=en',
				'pattern=type-' . $post_default->post_type . '-id-' . $post_default->ID . '-en|context=current|language_code=en',
			),
		);

		$this->go_to( '/?p=' . $post_default->ID );
		$this->run_tests( $language_code_to_descriptions_expected, Post::class );

		// Go to a post url in a non default language and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=type|context=current|language_code=all',
				'pattern=type-id-' . $post_alt->ID . '|context=current|language_code=all',
				'pattern=type-slug-' . $post_alt->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_alt->post_type . '|context=current|language_code=all',
				'pattern=type-' . $post_alt->post_type . '-slug-' . $post_alt->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_alt->post_type . '-id-' . $post_alt->ID . '|context=current|language_code=all',
			),
			'el'              => array(
				'pattern=type|context=current|language_code=all',
				'pattern=type-id-' . $post_default->ID . '|context=current|language_code=all',
				'pattern=type-slug-' . $post_default->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '-slug-' . $post_default->post_name . '|context=current|language_code=all',
				'pattern=type-' . $post_default->post_type . '-id-' . $post_default->ID . '|context=current|language_code=all',
				'pattern=type-el|context=current|language_code=el',
				'pattern=type-id-' . $post_default->ID . '-el|context=current|language_code=el',
				'pattern=type-slug-' . $post_default->post_name . '-el|context=current|language_code=el',
				'pattern=type-' . $post_default->post_type . '-el|context=current|language_code=el',
				'pattern=type-' . $post_default->post_type . '-slug-' . $post_default->post_name . '-el|context=current|language_code=el',
				'pattern=type-' . $post_default->post_type . '-id-' . $post_default->ID . '-el|context=current|language_code=el',
				'pattern=type-id-' . $post_alt->ID . '|context=current|language_code=el',
				'pattern=type-slug-' . $post_alt->post_name . '|context=current|language_code=el',
				'pattern=type-' . $post_alt->post_type . '-slug-' . $post_alt->post_name . '|context=current|language_code=el',
				'pattern=type-' . $post_alt->post_type . '-id-' . $post_alt->ID . '|context=current|language_code=el',
			),
		);

		$this->go_to( '/?p=' . $post_alt->ID );
		$this->run_tests( $language_code_to_descriptions_expected, Post::class );
	}

	/**
	 * Tests that all relevant Description instances are returned for a term page.
	 *
	 * @return void
	 */
	public function test_descriptor_term() {
		// Go to a non-term url and test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
			'en'              => array(),
			'el'              => array(),
		);

		$this->go_to( '/' );
		$this->run_tests( $language_code_to_descriptions_expected, Term::class );

		// Create dummy terms to test language based term Descriptions

		$term_default = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );
		$term_alt     = $this->factory()->term->create_and_get( array( 'taxonomy' => 'category' ) );

		$this->wpml_default_language_term_id = $term_default->term_id;
		$this->wpml_alt_language_term_id     = $term_alt->term_id;

		// Go to a term  url in the default language and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=term|context=current|language_code=all',
				'pattern=term-id-' . $term_default->term_id . '|context=current|language_code=all',
				'pattern=term-slug-' . $term_default->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '-term-slug-' . $term_default->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '-term-id-' . $term_default->term_id . '|context=current|language_code=all',
			),
			'en'              => array(
				'pattern=term|context=current|language_code=all',
				'pattern=term-id-' . $term_default->term_id . '|context=current|language_code=all',
				'pattern=term-slug-' . $term_default->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '-term-slug-' . $term_default->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '-term-id-' . $term_default->term_id . '|context=current|language_code=all',
				'pattern=term-en|context=current|language_code=en',
				'pattern=term-id-' . $term_default->term_id . '-en|context=current|language_code=en',
				'pattern=term-slug-' . $term_default->slug . '-en|context=current|language_code=en',
				'pattern=tax-' . $term_default->taxonomy . '-en|context=current|language_code=en',
				'pattern=tax-' . $term_default->taxonomy . '-term-slug-' . $term_default->slug . '-en|context=current|language_code=en',
				'pattern=tax-' . $term_default->taxonomy . '-term-id-' . $term_default->term_id . '-en|context=current|language_code=en',
			),
		);

		$this->go_to( get_term_link( $term_default ) );
		$this->run_tests( $language_code_to_descriptions_expected, Term::class );

		// Go to a term url in a non default language and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=term|context=current|language_code=all',
				'pattern=term-id-' . $term_alt->term_id . '|context=current|language_code=all',
				'pattern=term-slug-' . $term_alt->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_alt->taxonomy . '|context=current|language_code=all',
				'pattern=tax-' . $term_alt->taxonomy . '-term-slug-' . $term_alt->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_alt->taxonomy . '-term-id-' . $term_alt->term_id . '|context=current|language_code=all',
			),
			'el'              => array(
				'pattern=term|context=current|language_code=all',
				'pattern=term-id-' . $term_default->term_id . '|context=current|language_code=all',
				'pattern=term-slug-' . $term_default->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '-term-slug-' . $term_default->slug . '|context=current|language_code=all',
				'pattern=tax-' . $term_default->taxonomy . '-term-id-' . $term_default->term_id . '|context=current|language_code=all',
				'pattern=term-el|context=current|language_code=el',
				'pattern=term-id-' . $term_default->term_id . '-el|context=current|language_code=el',
				'pattern=term-slug-' . $term_default->slug . '-el|context=current|language_code=el',
				'pattern=tax-' . $term_default->taxonomy . '-el|context=current|language_code=el',
				'pattern=tax-' . $term_default->taxonomy . '-term-slug-' . $term_default->slug . '-el|context=current|language_code=el',
				'pattern=tax-' . $term_default->taxonomy . '-term-id-' . $term_default->term_id . '-el|context=current|language_code=el',
				'pattern=term-id-' . $term_alt->term_id . '|context=current|language_code=el',
				'pattern=term-slug-' . $term_alt->slug . '|context=current|language_code=el',
				'pattern=tax-' . $term_alt->taxonomy . '-term-slug-' . $term_alt->slug . '|context=current|language_code=el',
				'pattern=tax-' . $term_alt->taxonomy . '-term-id-' . $term_alt->term_id . '|context=current|language_code=el',
			),
		);

		$this->go_to( get_term_link( $term_alt ) );
		$this->run_tests( $language_code_to_descriptions_expected, Term::class );
	}

	/**
	 * Tests that all relevant Description instances are returned for the search page.
	 *
	 * @return void
	 */
	public function test_descriptor_search() {
		// Go to a non-search url and test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
			'en'              => array(),
			'el'              => array(),
		);

		$this->go_to( '/' );
		$this->run_tests( $language_code_to_descriptions_expected, Search::class );

		// Go to a search url and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=search|context=current|language_code=all',
			),
			'en'              => array(
				'pattern=search|context=current|language_code=all',
				'pattern=search-en|context=current|language_code=en',
			),
		);

		$this->go_to( '/?s=test' );
		$this->run_tests( $language_code_to_descriptions_expected, Search::class );
	}

	/**
	 * Tests that all relevant Description instances are returned for a user's archive page.
	 *
	 * @return void
	 */
	public function test_descriptor_user() {
		// Go to a non-user url and test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
		);

		$this->go_to( '/' );
		$this->run_tests( $language_code_to_descriptions_expected, User::class );

		// Go to an author url and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=user|context=current|language_code=all',
				'pattern=user-id-1|context=current|language_code=all',
			),
		);

		$this->go_to( get_author_posts_url( 1 ) );
		$this->run_tests( $language_code_to_descriptions_expected, User::class );
	}

	/**
	 * Tests that all relevant Description instances are returned for a date archive page.
	 *
	 * @return void
	 */
	public function test_descriptor_archive_date() {
		// Go to a non date archive url and test that no Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(),
			'en'              => array(),
			'el'              => array(),
		);

		$this->go_to( '/' );
		$this->run_tests( $language_code_to_descriptions_expected, Archive::class );

		// Go to a date archive url and test that all relevant Description instances are returned.

		$language_code_to_descriptions_expected = array(
			'no_multilingual' => array(
				'pattern=archive|context=current|language_code=all',
				'pattern=archive-date|context=current|language_code=all',
			),
			'en'              => array(
				'pattern=archive|context=current|language_code=all',
				'pattern=archive-date|context=current|language_code=all',
				'pattern=archive-en|context=current|language_code=en',
				'pattern=archive-date-en|context=current|language_code=en',
			),
			'el'              => array(
				'pattern=archive|context=current|language_code=all',
				'pattern=archive-date|context=current|language_code=all',
				'pattern=archive-el|context=current|language_code=el',
				'pattern=archive-date-el|context=current|language_code=el',
			),
		);

		$this->go_to( get_day_link( false, false, false ) );
		$this->run_tests( $language_code_to_descriptions_expected, Archive::class );
	}
}
