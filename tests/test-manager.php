<?php

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Manager;

class TestManager extends WP_UnitTestCase
{
	/**
	 * @return array An associative array of configuration values according to which dummy Asset instances are created.
	 */
	private function get_asset_generation_configuration(): array
	{
		return array(
			'asset_type_to_extensions' => array(
				'scripts' => array( 'js', 'js.php' ),
				'stylesheets' => array( 'css', 'css.php' ),
			),
			'contexts' => array( 'global', 'current' ),
			'language_codes' => array( 'all', 'en', 'el' ),
			'locations' => array( 'head', 'footer' ),
			'sources' => array( 'external', 'internal' ),
		);
	}

	/**
	 * Generates and returns an array of dummy Asset instances according to the provided configuration.
	 *
	 * @param array $configuration An associative array of configuration values for the generation process.
	 * @return Asset[] An array of dummy Asset instances created according to the provided configuration.
	 */
	private function generate_assets( array $configuration ): array
	{
		extract( $configuration );

		foreach ( $asset_type_to_extensions as $asset_type => $extensions ) {
			foreach ( $extensions as $extension ) {
				foreach ( $contexts as $context ) {
					foreach ( $language_codes as $language_code ) {
						foreach ( $locations as $location ) {
							foreach ( $sources as $source ) {
								$filepath = tempnam( sys_get_temp_dir(), "$asset_type-$extension-$context-$language_code-$location-$source" );
								$assets[] = new Asset(
									$asset_type,
									$extension,
									$filepath,
									$filepath,
									basename( $filepath ),
									$context,
									$language_code,
									array( 'source' => $source, 'location' => $location )
								);
							}
						}
					}
				}
			}
		}

		return $assets;
	}

	/**
	 * Tests the Asset filtering logic of the Manager class.
	 *
	 * @return void
	 */
	public function test_asset_filtering()
	{
		$assets = $this->generate_assets( $this->get_asset_generation_configuration() );

		foreach ( Manager::get_assets_filtered( $assets, 'head', array( 'external' ) ) as $asset ) {
			$this->assertMatchesRegularExpression( "/(scripts|stylesheets)-([a-z\-.]*)-head-external/", $asset->get_absolute_filepath(), "Return only script or stylesheet assets with 'head' and 'external' flags." );
		}

		foreach ( Manager::get_assets_filtered( $assets, 'head', array( 'internal' ) ) as $asset ) {
			$this->assertMatchesRegularExpression( "/(scripts|stylesheets)-([a-z\-.]*)-head-internal/", $asset->get_absolute_filepath(), "Return only script or stylesheet assets with 'head' and 'internal' flags." );
		}

		foreach ( Manager::get_assets_filtered( $assets, 'head', array( 'external', 'internal' ) ) as $asset ) {
			$this->assertMatchesRegularExpression( "/(scripts|stylesheets)-([a-z\-.]*)-head-(external|internal)/", $asset->get_absolute_filepath(), "Return only script or stylesheet assets with 'head', 'external' or 'internal' flags." );
		}

		foreach ( Manager::get_assets_filtered( $assets, 'footer', array( 'external' ) ) as $asset ) {
			$this->assertMatchesRegularExpression( "/(scripts|stylesheets)-([a-z\-.]*)-footer-external/", $asset->get_absolute_filepath(), "Return only script or stylesheet assets with 'footer' and 'external' flags." );
		}

		foreach ( Manager::get_assets_filtered( $assets, 'footer', array( 'internal' ) ) as $asset ) {
			$this->assertMatchesRegularExpression( "/(scripts|stylesheets)-([a-z\-.]*)-footer-internal/", $asset->get_absolute_filepath(), "Return only script or stylesheet assets with 'footer' and 'internal' flags." );
		}
	}

	/**
	 * Tests the Asset sorting logic of the Manager class.
	 *
	 * @return void
	 */
	public function test_asset_sorting()
	{
		$configuration = $this->get_asset_generation_configuration();

		extract( $configuration );

		$strings_expected = array();

		foreach ( $contexts as $context ) {
			foreach ( $language_codes as $language_code ) {
				$strings_expected[] = $context . '-' . $language_code;
			}
		}

		$asset_type_to_strings_found = array();

		foreach ( Manager::get_assets_sorted( $this->generate_assets( $configuration ) ) as $asset ) {
			$matches = array();
			if ( preg_match('/[a-z]*-[a-z]*-(.*)-(.*)-[a-z]*-[a-z]*/', $asset->get_absolute_filepath(), $matches ) ) {
				$asset_type_to_strings_found[ $asset->get_type() ][] = $matches[1] . '-' . $matches[2];
			}
		}

		foreach ( $asset_type_to_strings_found as $strings_found ) {
			$strings_found = array_unique( $strings_found );
			$this->assertEquals(
				implode( '|', $strings_expected ), implode( '|', $strings_found ),
				'Assets are returned sorted in the order: global context -> language agnostic -> language based -> current context -> language agnostic -> language based'
			);
		}
	}
}
