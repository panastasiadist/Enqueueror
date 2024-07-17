<?php

use panastasiadist\Enqueueror\Descriptors\Archive;
use panastasiadist\Enqueueror\Descriptors\Generic;
use panastasiadist\Enqueueror\Descriptors\NotFound;
use panastasiadist\Enqueueror\Descriptors\Post;
use panastasiadist\Enqueueror\Descriptors\Search;
use panastasiadist\Enqueueror\Descriptors\Term;
use panastasiadist\Enqueueror\Descriptors\User;
use panastasiadist\Enqueueror\Explorer;
use panastasiadist\Enqueueror\Support\Language\WPMLMediator;

require_once( __DIR__ . '/wpml-boilerplate.php' );

class TestExplorer extends WP_UnitTestCase {
	use WPML_Boilerplate;

	private $asset_type_to_directory_path = array();

	/**
	 * Deletes the directory specified by the provided path.
	 *
	 * @param string $directory_path Absolute filesystem path to a directory to delete.
	 *
	 * @return void
	 */
	private function remove_directory( string $directory_path ) {
		foreach ( glob( $directory_path . '/*' ) as $file ) {
			if ( is_dir( $file ) ) {
				$this->remove_directory( $file );
			} else {
				unlink( $file );
			}
		}

		rmdir( $directory_path );
	}

	/**
	 * Creates and returns a new Explorer instance, configured with dummy asset paths as required for the tests.
	 *
	 * @return Explorer
	 */
	private function explorer(): Explorer {
		$paths = $this->asset_type_to_directory_path;

		$config = array(
			'scripts'     => array(
				'extensions'     => array( 'js', 'js.php' ),
				'directory_path' => $paths['scripts'],
			),
			'stylesheets' => array(
				'extensions'     => array( 'css', 'css.php' ),
				'directory_path' => $paths['stylesheets'],
			),
		);

		$descriptors = array(
			new Archive( new WPMLMediator() ),
			new Generic( new WPMLMediator() ),
			new NotFound( new WPMLMediator() ),
			new Post( new WPMLMediator() ),
			new Search( new WPMLMediator() ),
			new Term( new WPMLMediator() ),
			new User( new WPMLMediator() ),
		);

		return new Explorer( $config, $descriptors );
	}

	/**
	 * Runs before a test method is called to prepare the ground for the test to be run.
	 *
	 * @return void
	 */
	public function setUp(): void {
		$this->setUpWPML();

		$base_directory = tempnam( sys_get_temp_dir(), 'test' );
		unlink( $base_directory );

		$this->asset_type_to_directory_path['scripts']     = $base_directory . '/scripts';
		$this->asset_type_to_directory_path['stylesheets'] = $base_directory . '/stylesheets';

		wp_mkdir_p( $this->asset_type_to_directory_path['scripts'] );
		wp_mkdir_p( $this->asset_type_to_directory_path['stylesheets'] );
	}

	/**
	 * Runs after a test method has run to clean up stuff.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		$this->tearDownWPML();

		foreach ( $this->asset_type_to_directory_path as $directory_path ) {
			$this->remove_directory( $directory_path );
		}
	}

	/**
	 * Tests that Explorer detects and returns only the appropriate Asset instances.
	 *
	 * @return void
	 */
	public function test_get_assets() {
		$paths    = $this->asset_type_to_directory_path;
		$explorer = $this->explorer();

		// Create dummy asset files, specifying which of them should be returned as applicable to the request.

		$asset_type_to_scenarios = array(
			'scripts'     => array(
				array( 'file_path' => DIRECTORY_SEPARATOR . 'global.js', 'applicable' => true ),
				array( 'file_path'  => DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'global.js.php', 'applicable' => true ),
				array( 'file_path' => DIRECTORY_SEPARATOR . 'type.js', 'applicable' => false ),
				array( 'file_path'  => DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'type.js.php', 'applicable' => false ),
			),
			'stylesheets' => array(
				array( 'file_path' => DIRECTORY_SEPARATOR . 'global.css', 'applicable' => true ),
				array( 'file_path'  => DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'global.css.php', 'applicable' => true ),
				array( 'file_path' => DIRECTORY_SEPARATOR . 'type.css', 'applicable' => false ),
				array( 'file_path'  => DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'type.css.php', 'applicable' => false ),
			),
		);

		foreach ( $asset_type_to_scenarios as $asset_type => $scenarios ) {
			foreach ( $scenarios as $scenario ) {
				$absolute_filepath = $paths[ $asset_type ] . $scenario['file_path'];

				$file_path_parts      = explode( DIRECTORY_SEPARATOR, $absolute_filepath );
				$directory_path_parts = array_slice( $file_path_parts, 0, count( $file_path_parts ) - 1 );
				$directory_path       = implode( DIRECTORY_SEPARATOR, $directory_path_parts );

				wp_mkdir_p( $directory_path );

				touch( $absolute_filepath );
			}
		}

		// We are in the default home page of the WordPress installation set up for the tests.
		// The default home page is not of a specific post type.
		// So, only global context assets should be returned.

		foreach ( array( 'stylesheets', 'scripts' ) as $asset_type ) {
			$scenarios = $asset_type_to_scenarios[ $asset_type ];

			$scenarios = array_filter( $scenarios, function ( $scenario ) {
				return $scenario['applicable'];
			} );

			$relative_filepaths_expected = array_map( function ( $scenario ) {
				return $scenario['file_path'];
			}, $scenarios );

			sort( $relative_filepaths_expected );

			$assets = $explorer->get_assets( $asset_type );

			$relative_filepaths_actual = array_map( function ( $asset ) {
				return $asset->get_relative_filepath();
			}, $assets );

			sort( $relative_filepaths_actual );

			$this->assertEquals( $relative_filepaths_expected, $relative_filepaths_actual );
		}
	}

	/**
	 * Tests that Explorer correctly identifies an asset's characteristics by file path, throwing an Exception if an
	 * invalid asset type is provided.
	 *
	 * @return void
	 */
	public function test_get_asset_for_file_path() {
		$paths    = $this->asset_type_to_directory_path;
		$explorer = $this->explorer();

		$scenarios = array(
			array(
				'type'              => 'scripts',
				'extension'         => 'js',
				'filename'          => 'global',
				'relative_filepath' => DIRECTORY_SEPARATOR . 'global.js',
				'context'           => 'global',
				'language_code'     => 'all',
				'flags'             => array( 'source' => 'external', 'location' => 'head' ),
			),
			array(
				'type'              => 'scripts',
				'extension'         => 'js',
				'filename'          => 'type-page-slug-home-en.head',
				'relative_filepath' => DIRECTORY_SEPARATOR . 'type-page-slug-home-en.head.js',
				'context'           => 'current',
				'language_code'     => 'en',
				'flags'             => array( 'source' => 'external', 'location' => 'head' ),
			),
			array(
				'type'              => 'stylesheets',
				'extension'         => 'css.php',
				'filename'          => 'global',
				'relative_filepath' => DIRECTORY_SEPARATOR . 'global.css.php',
				'context'           => 'global',
				'language_code'     => 'all',
				'flags'             => array( 'source' => 'external', 'location' => 'head' ),
			),
			array(
				'type'              => 'stylesheets',
				'extension'         => 'css.php',
				'filename'          => 'type-page-slug-home-el.footer.internal',
				'relative_filepath' => DIRECTORY_SEPARATOR . 'type-page-slug-home-el.footer.internal.css.php',
				'context'           => 'current',
				'language_code'     => 'el',
				'flags'             => array( 'source' => 'internal', 'location' => 'footer' ),
			),
		);

		foreach ( $scenarios as $scenario ) {
			$absolute_filepath = $paths[ $scenario['type'] ] . $scenario['relative_filepath'];
			touch( $absolute_filepath );

			if ( $scenario['language_code'] ) {
				if ( 'all' !== $scenario['language_code'] ) {
					$this->wpml_current_language = $scenario['language_code'];
				} else {
					$this->wpml_current_language = null;
				}
			}

			$asset = $explorer->get_asset_for_file_path( $absolute_filepath, $scenario['type'] );

			$this->assertEquals( $scenario['type'], $asset->get_type() );
			$this->assertEquals( $scenario['extension'], $asset->get_extension() );
			$this->assertEquals( $absolute_filepath, $asset->get_absolute_filepath() );
			$this->assertEquals( $scenario['relative_filepath'], $asset->get_relative_filepath() );
			$this->assertEquals( $scenario['filename'], $asset->get_filename() );
			$this->assertEquals( $scenario['context'], $asset->get_context() );
			$this->assertEquals( basename( $absolute_filepath ), $asset->get_basename() );

			foreach ( $scenario['flags'] as $flag => $value ) {
				$this->assertEquals( $value, $asset->get_flag( $flag ) );
			}
		}

		$this->expectException( Exception::class );

		$explorer->get_asset_for_file_path( '', 'invalid_asset_type' );
	}
}
