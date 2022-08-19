<?php

use panastasiadist\Enqueueror\Base\Asset;

class TestAsset extends WP_UnitTestCase
{
	/**
	 * Tests the functionality of the Asset class.
	 */
	public function test()
	{
		$absolute_css_filepath = tempnam( sys_get_temp_dir(), 'test' ) . '.css';
		touch( $absolute_css_filepath );

		$dummy_relative_css_filepath = $absolute_css_filepath;

		$asset = new Asset(
			'stylesheets',
			'css',
			$absolute_css_filepath,
			$dummy_relative_css_filepath,
			'file.css',
			'global',
			'all',
			array( 'flag' )
		);

		$this->assertEquals( 'stylesheets', $asset->get_type() );
		$this->assertEquals( 'css', $asset->get_extension() );
		$this->assertEquals( $absolute_css_filepath, $asset->get_absolute_filepath() );
		$this->assertEquals( $absolute_css_filepath, $asset->get_relative_filepath() );
		$this->assertEquals( 'file.css', $asset->get_filename() );
		$this->assertEquals( 'global', $asset->get_context() );
		$this->assertEquals( 'all', $asset->get_language_code() );
		$this->assertEquals( array( 'flag' ), $asset->get_flags() );
		$this->assertEquals( basename( $absolute_css_filepath ), $asset->get_basename() );
	}
}
