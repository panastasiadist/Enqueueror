<?php

use panastasiadist\Enqueueror\Processors\CSS\Php as ProcessorCSSPhp;
use panastasiadist\Enqueueror\Processors\CSS\Raw as ProcessorCSSRaw;
use panastasiadist\Enqueueror\Processors\JS\Php as ProcessorJSPhp;
use panastasiadist\Enqueueror\Processors\JS\Raw as ProcessorJSRaw;

class TestProcessors extends WP_UnitTestCase {
	/**
	 * Returns a set of header value configuration scenarios and the relevant dummy asset files implementing the
	 * scenarios. The scenarios are meant to be used in assertions by having the processors read the header values
	 * written in the generated dummy asset files.
	 *
	 * @return array An array of scenario configurations.
	 */
	private function generate_asset_header_scenarios(): array {
		$scenarios = array(
			array(
				'content'  =>
					'/*** RequiresA   :/testA1.css,/testA2.js, /subfolder//testE2-dash-under_score.js **/',
				'expected' => array(
					'RequiresA' => '/testA1.css,/testA2.js, /subfolder//testE2-dash-under_score.js ',
				),
			),
			array(
				'content'  =>
					'/* RequiresA:/testA1.css,/testA2.js' . PHP_EOL .
					' * RequiresB :/testB1.css,/testB2.js' . PHP_EOL .
					' * RequiresC : /testC1.css,/testC2 with space.js' . PHP_EOL .
					' * RequiresD : /testD1.css, /testD2_with_underscore.js' . PHP_EOL .
					' * RequiresE : /testE1.css, /testE2-dash-under_score.js ' . PHP_EOL .
					' *     RequiresF: /testF1.css, /subfolder/testF2.js **' . PHP_EOL .
					' */',
				'expected' => array(
					'RequiresA' => '/testA1.css,/testA2.js',
					'RequiresB' => '/testB1.css,/testB2.js',
					'RequiresC' => ' /testC1.css,/testC2 with space.js',
					'RequiresD' => ' /testD1.css, /testD2_with_underscore.js',
					'RequiresE' => ' /testE1.css, /testE2-dash-under_score.js ',
					'RequiresF' => ' /testF1.css, /subfolder/testF2.js ',
				),
			),
		);

		foreach ( $scenarios as &$scenario ) {
			$file_path = tempnam( sys_get_temp_dir(), 'test' );
			file_put_contents( $file_path, $scenario['content'] );
			$scenario['file_path'] = $file_path;
		}

		return $scenarios;
	}

	/**
	 * Tests the functionality of panastasiadist\Enqueueror\Flags\Location.
	 */
	public function test_css_processor_php() {
		$css_raw          = '.class1 { padding: 0 }';
		$css_php          = '<?php echo "' . $css_raw . '"; ?>';
		$css_php_filepath = tempnam( sys_get_temp_dir(), 'test' ) . '.css.php';
		file_put_contents( $css_php_filepath, $css_php );

		$this->assertEquals( $css_raw, file_get_contents( ProcessorCSSPhp::get_processed_filepath( $css_php_filepath ) ), "CSS PHP processing" );
		$this->assertEquals( false, ProcessorCSSPhp::get_processed_filepath( $css_php_filepath . 'non_existent' ), "CSS PHP processing returns false for non existent asset" );
		$this->assertEquals( 'stylesheets', ProcessorCSSPhp::get_supported_asset_type(), "The value 'stylesheets' is returned as the supported asset type of the PHP CSS processor." );
		$this->assertEquals( array( 'css.php' ), ProcessorCSSPhp::get_supported_extensions(), "The extension 'css.php' is supported by the PHP CSS processor." );

		foreach ( $this->generate_asset_header_scenarios() as $scenario ) {
			$this->assertEquals( $scenario['expected'], ProcessorCSSPhp::get_header_values( $scenario['file_path'] ) );
		}
	}

	public function test_css_processor_raw() {
		$css_raw          = '.class1 { padding: 0 }';
		$css_raw_filepath = tempnam( sys_get_temp_dir(), 'test' ) . '.css';
		file_put_contents( $css_raw_filepath, $css_raw );

		$this->assertEquals( $css_raw, file_get_contents( ProcessorCSSRaw::get_processed_filepath( $css_raw_filepath ) ), "CSS RAW processing" );
		$this->assertEquals( false, ProcessorCSSRaw::get_processed_filepath( $css_raw_filepath . 'non_existent' ), "CSS RAW processing returns false for non existent asset" );
		$this->assertEquals( 'stylesheets', ProcessorCSSRaw::get_supported_asset_type(), "The value 'stylesheets' is returned as the supported asset type of the RAW CSS processor." );
		$this->assertEquals( array( 'css' ), ProcessorCSSRaw::get_supported_extensions(), "The extension 'css' is supported by the PHP CSS processor." );

		foreach ( $this->generate_asset_header_scenarios() as $scenario ) {
			$this->assertEquals( $scenario['expected'], ProcessorCSSRaw::get_header_values( $scenario['file_path'] ) );
		}
	}

	/**
	 * Tests the functionality of panastasiadist\Enqueueror\Flags\Source.
	 */
	public function test_js_processor_php() {
		$js_raw          = 'var test = true;';
		$js_php          = '<?php echo "' . $js_raw . '"; ?>';
		$js_php_filepath = tempnam( sys_get_temp_dir(), 'test' ) . '.js.php';
		file_put_contents( $js_php_filepath, $js_php );

		$this->assertEquals( $js_raw, file_get_contents( ProcessorJSPhp::get_processed_filepath( $js_php_filepath ) ), "JS PHP processing" );
		$this->assertEquals( false, ProcessorJSPhp::get_processed_filepath( $js_php_filepath . 'non_existent' ), "JS PHP processing returns false for non existent asset" );
		$this->assertEquals( 'scripts', ProcessorJSPhp::get_supported_asset_type(), "The value 'stylesheets' is returned as the supported asset type of the PHP JS processor." );
		$this->assertEquals( array( 'js.php' ), ProcessorJSPhp::get_supported_extensions(), "The extension 'js.php' is supported by the JS CSS processor." );

		foreach ( $this->generate_asset_header_scenarios() as $scenario ) {
			$this->assertEquals( $scenario['expected'], ProcessorJSPhp::get_header_values( $scenario['file_path'] ) );
		}
	}

	public function test_js_processor_raw() {
		$js_raw          = 'var test = true;';
		$js_raw_filepath = tempnam( sys_get_temp_dir(), 'test' ) . '.js';
		file_put_contents( $js_raw_filepath, $js_raw );

		$this->assertEquals( $js_raw, file_get_contents( ProcessorJSRaw::get_processed_filepath( $js_raw_filepath ) ), "JS RAW processing" );
		$this->assertEquals( false, ProcessorJSRaw::get_processed_filepath( $js_raw_filepath . 'non_existent' ), "JS RAW processing returns false for non existent asset" );
		$this->assertEquals( 'scripts', ProcessorJSRaw::get_supported_asset_type(), "The value 'stylesheets' is returned as the supported asset type of the RAW JS processor." );
		$this->assertEquals( array( 'js' ), ProcessorJSRaw::get_supported_extensions(), "The extension 'js' is supported by the JS CSS processor." );

		foreach ( $this->generate_asset_header_scenarios() as $scenario ) {
			$this->assertEquals( $scenario['expected'], ProcessorJSRaw::get_header_values( $scenario['file_path'] ) );
		}
	}
}
