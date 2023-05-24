<?php

use panastasiadist\Enqueueror\Base\Description;

class TestDescription extends WP_UnitTestCase {
	/**
	 * Tests the functionality of the Descriptor class.
	 */
	public function test() {
		$pattern = 'a_regex_pattern';

		$instance = new Description( $pattern );
		$this->assert_values( $instance, $pattern, 'current', 'all', "The 'current' and 'all' values are set as the default for context and language code arguments, respectively." );

		$instance = new Description( $pattern, 'current' );
		$this->assert_values( $instance, $pattern, 'current', 'all', "The 'current' value is set for the context argument." );

		$instance = new Description( $pattern, 'global' );
		$this->assert_values( $instance, $pattern, 'global', 'all', "The 'global' value is set for the context argument." );

		$instance = new Description( $pattern, 'global', 'el' );
		$this->assert_values( $instance, $pattern, 'global', 'el', "The 'global' and 'el' values are set for the context and language code arguments, respectively." );

		$this->expectException( InvalidArgumentException::class );
		new Description( $pattern, 'invalid_context', 'all' );

		$this->expectException( InvalidArgumentException::class );
		new Description( $pattern, 'global', '' );
	}

	private function assert_values( Description $instance, string $name, string $context, string $language_code, string $message = '' ) {
		$this->assertEquals( $name, $instance->get_pattern(), $message );
		$this->assertEquals( $context, $instance->get_context(), $message );
		$this->assertEquals( $language_code, $instance->get_language_code(), $message );
	}
}
