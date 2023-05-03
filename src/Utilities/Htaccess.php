<?php

namespace panastasiadist\Enqueueror\Utilities;

class Htaccess {
	/**
	 * Write htaccess rules
	 *
	 * @param string[] $paths_to_restrict An array of paths to block public access to using .htaccess rules.
	 *
	 * @return void
	 */
	public static function write( array $paths_to_restrict ) {
		$htaccess_marker_lines = array(
			'<IfModule mod_rewrite.c>',
			'RewriteEngine On',
		);

		// Reset the array keys to make sure that zero based indexing is possible.
		$paths_to_restrict = array_values( $paths_to_restrict );

		$count = count( $paths_to_restrict );

		for ( $idx = 0; $idx < $count; $idx += 1 ) {
			$rule = 'RewriteCond %{REQUEST_FILENAME} ^' . $paths_to_restrict[ $idx ] . '/.+\.php$';

			if ( $idx < $count - 1 ) {
				$rule .= ' [OR]';
			}

			$htaccess_marker_lines[] = $rule;
		}

		$htaccess_marker_lines[] = 'RewriteRule .* - [F]';
		$htaccess_marker_lines[] = '</IfModule>';

		$htaccess_filepath = get_home_path() . '.htaccess';
		insert_with_markers( $htaccess_filepath, "Enqueueror", $htaccess_marker_lines );
	}

	/**
	 * Remove any htaccess rules written by the plugin
	 *
	 * @return void
	 */
	public static function delete() {
		$htaccess_filepath = get_home_path() . '.htaccess';
		insert_with_markers( $htaccess_filepath, "Enqueueror", array() );
	}
}