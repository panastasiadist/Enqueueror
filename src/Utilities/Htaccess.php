<?php

namespace panastasiadist\Enqueueror\Utilities;

use panastasiadist\Enqueueror\Explorer;

class Htaccess
{
    /**
     * Write htaccess rules
     *
     * @return void
     */
    public static function write()
    {
        $htaccess_marker_lines = array(
            '<IfModule mod_rewrite.c>',
            'RewriteEngine On',
        );

        $explorer = new Explorer( wp_get_theme()->get_stylesheet_directory() );
        $paths = array_values( $explorer->get_asset_directory_paths() );
        $count = count( $paths );

        for ( $idx = 0; $idx < $count; $idx += 1 ) {
            $rule = 'RewriteCond %{REQUEST_FILENAME} ^' . $paths[ $idx ] . '/.+\.php$';

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
    public static function delete()
    {
        $htaccess_filepath = get_home_path() . '.htaccess';
        insert_with_markers( $htaccess_filepath, "Enqueueror", array() );
    } 
}