<?php

use panastasiadist\Enqueueror\Utilities\Htaccess;

class TestHtaccess extends WP_UnitTestCase {
	/**
	 * Tests the functionality of the Htaccess utility class
	 */
	public function test() {
		$paths = array(
			'directory1/subdirectoryA',
			'directory1/subdirectoryB',
			'directory2/subdirectoryA',
			'directory2/subdirectoryB',
		);

		Htaccess::write( $paths );

		$htaccess_filepath = get_home_path() . '.htaccess';

		$htaccess_contents = file_get_contents( $htaccess_filepath );

		$rules[] = '<IfModule mod_rewrite.c>';
		$rules[] = 'RewriteEngine On';

		for ( $index = 0; $index < count( $paths ); $index += 1 ) {
			$path    = $paths[ $index ];
			$rules[] = "RewriteCond %{REQUEST_FILENAME} ^$path/.+\.php$" . ( ( $index < count( $paths ) - 1 ) ? ' [OR]' : '' );
		}

		$rules[] = 'RewriteRule .* - [F]';
		$rules[] = '</IfModule>';

		$rules = implode( PHP_EOL, $rules );

		$this->assertStringContainsString( $rules, $htaccess_contents, "Write .htaccess rules blocking access to specific directories" );

		Htaccess::delete();

		$htaccess_contents = file_get_contents( $htaccess_filepath );

		$this->assertStringNotContainsString( $rules, $htaccess_contents, "Deleted .htaccess rules" );
	}
}
