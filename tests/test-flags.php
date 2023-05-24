<?php

use panastasiadist\Enqueueror\Flags\Source;
use panastasiadist\Enqueueror\Flags\Location;

class TestFlags extends WP_UnitTestCase {
	/**
	 * Tests the functionality of Location flag class.
	 */
	public function test_location_flag() {
		$this->assertEquals( 'location', Location::get_name(), "The value 'location' is returned as the name of the flag." );
		$this->assertEquals( 'head', Location::get_detected_value( array( 'head' ) ), "The 'head' value is detected and supported by the flag." );
		$this->assertEquals( 'footer', Location::get_detected_value( array( 'footer' ) ), "The 'footer' value is detected and supported by the flag." );
		$this->assertEquals( 'head', Location::get_detected_value( array( 'footer', 'head' ) ), "Only the last value is returned when trying to detect more than 1 values" );
		$this->assertEquals( 'head', Location::get_detected_value( array( 'footer', 'head' ), 'default_value' ), "Only the last value is returned when trying to detect more than 1 values, ignoring the provided default value." );
		$this->assertEquals( 'head', Location::get_detected_value( array( 'non_supported_value' ) ), "The default value is returned when trying to detect a value not supported by the flag." );
		$this->assertEquals( "default_value", Location::get_detected_value( array( 'non_supported_value' ), 'default_value' ), "The provided default value is returned when trying to detect a value not supported by the flag." );
		$this->assertEquals( "default_value", Location::get_detected_value( array(), 'default_value' ), "The provided default value is returned when an empty array of values is provided." );
	}

	/**
	 * Tests the functionality of Source flag class.
	 */
	public function test_source_flag() {
		$this->assertEquals( 'source', Source::get_name(), "The value 'source' is returned as the name of the flag." );
		$this->assertEquals( 'internal', Source::get_detected_value( array( 'internal' ) ), "The 'internal' value is detected and supported by the flag." );
		$this->assertEquals( 'external', Source::get_detected_value( array( 'external' ) ), "The 'external' value is detected and supported by the flag." );
		$this->assertEquals( 'internal', Source::get_detected_value( array( 'external', 'internal' ) ), "Only the last value is returned when trying to detect more than 1 values" );
		$this->assertEquals( 'internal', Source::get_detected_value( array( 'external', 'internal' ), 'default_value' ), "Only the last value is returned when trying to detect more than 1 values, ignoring the provided default value." );
		$this->assertEquals( 'external', Source::get_detected_value( array( 'non_supported_value' ) ), "The default value is returned when trying to detect a value not supported by the flag." );
		$this->assertEquals( "default_value", Source::get_detected_value( array( 'non_supported_value' ), 'default_value' ), "The provided default value is returned when trying to detect a value not supported by the flag." );
		$this->assertEquals( "default_value", Source::get_detected_value( array(), 'default_value' ), "The provided default value is returned when an empty array of values is provided." );
	}
}