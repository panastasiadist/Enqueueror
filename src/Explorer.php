<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Base\AssetNameRule;

class Explorer
{
    const SCRIPTS_DIRECTORY_NAME = 'scripts';
    const STYLESHEETS_DIRECTORY_NAME = 'stylesheets';

    private $asset_type_to_directory_path = array(
        'scripts' => '',
        'stylesheets' => '',
    );

    private $asset_type_to_supported_extensions = array(
        'scripts' => array(),
        'stylesheets' => array(),
    );

    private function get_files( string $directory_path )
    {
        if ( ! is_readable( $directory_path ) ) {
            return false;
        }

        // Stored in ascending order.
        $filepaths = array();
        $filedirpaths = array();
        
        foreach ( scandir( $directory_path, SCANDIR_SORT_ASCENDING ) as $basename ) {
            if ( '.' === $basename || '..' === $basename ) {
                continue;
            }

            $path = $directory_path . DIRECTORY_SEPARATOR . $basename;

            if ( is_file( $path ) ) {
                $filepaths[] = $path;
            } elseif ( is_dir( $path ) ) {
                $filedirpaths[] = $path;
            }
        }

        // Process the directories found after having processed the files in the current folder in order to enforce
        // ascending depth.
        foreach ( $filedirpaths as $path ) {
            $sub_files = $this->get_files( $path );

            if ( false !== $sub_files ) {
                $filepaths = array_merge( $filepaths, $sub_files );
            }
        }

        return $filepaths;
    }

    private function get_file_info_structures( string $directory_path, string $filename_regex ) 
    {
        $matched_files = array();

        $filepaths = $this->get_files( $directory_path );

        if ( false === $filepaths ) {
            return array();
        }

        foreach ( $filepaths as $filepath ) {
            $info = pathinfo( $filepath );

            if ( preg_match( $filename_regex, $info[ 'basename' ] ) ) {
                $matched_files[] = $info;
            }
        }

        return $matched_files;
    }

    private function get_language_augmented_asset_name_rules( array $asset_name_rules )
    {
        if ( class_exists( 'SitePress' ) ) {
            global $sitepress;
            $current_langcode = $sitepress->get_current_language();

            $asset_name_rules = array_merge($asset_name_rules, array_map(function( $asset_name_rule ) use ( $current_langcode ) {
                return new AssetNameRule(
                    $asset_name_rule->get_name() . '-' . $current_langcode,
                    $asset_name_rule->get_context(),
                    $current_langcode
                );
            }, $asset_name_rules));
        }

        return $asset_name_rules;
    }

    public function __construct( string $assets_directory_path )
    {
        $this->asset_type_to_directory_path[ 'scripts' ] = 
            $assets_directory_path . DIRECTORY_SEPARATOR . self::SCRIPTS_DIRECTORY_NAME;
        
        $this->asset_type_to_directory_path[ 'stylesheets' ] = 
            $assets_directory_path . DIRECTORY_SEPARATOR . self::STYLESHEETS_DIRECTORY_NAME;
    }

    public function register_asset_extensions( string $asset_type, array $extensions )
    {
        if ( isset( $this->asset_type_to_supported_extensions[ $asset_type ] ) ) {
            $this->asset_type_to_supported_extensions[ $asset_type ] = $extensions;
        } else {
            throw new \Exception( "Extensions for asset type '$asset_type' are not supported" );
        }
    }

    /**
     * Searches the filesystem for available asset files returning an array of Asset instances.
     *
     * @param AssetName[] $asset_names An array of AssetName instances containing candidate asset names to search for.
     * @param string $asset_type A string representing the type that the found asset files are designated for. 
     * Valid values are 'scripts' and 'stylesheets'.
     * @return Asset[] An array of Asset instances representing all found asset files. 
     */
    private function get_assets( array $asset_name_rules, string $asset_type )
    {
        if ( isset( $this->asset_type_to_directory_path[ $asset_type ] ) ) {
            $directory_path = $this->asset_type_to_directory_path[ $asset_type ];
        } else {
            throw new \Exception( "No asset directory registered for '$asset_type' asset type" );
        }

        if ( isset( $this->asset_type_to_supported_extensions[ $asset_type ] ) ) {
            $extensions = $this->asset_type_to_supported_extensions[ $asset_type ];
        } else {
            throw new \Exception( "No extensions registered for '$asset_type' asset type" );
        }

        $extensions = array_map(function($extension) {
            return '.' . $extension;
        }, $extensions);

        $extension_regex = str_replace( '.', '\.', implode( '|', $extensions ) );

        $asset_name_to_asset_name_rule = array_reduce($asset_name_rules, function( $map, $asset_name_rule ) {
            $map[ $asset_name_rule->get_name() ] = $asset_name_rule;
            return $map;
        }, array());

        $filenames_regex = implode( '|', array_keys( $asset_name_to_asset_name_rule ) );
        $filenames_regex = str_replace( '.', '\.', $filenames_regex );
        $filename_regex = "/^($filenames_regex)(\.[a-zA-Z0-9\-_\.]*)?($extension_regex)$/";

        $assets = array();

        // Sort the extensions, so the more lengthy come first.
        usort($extensions, function( $a, $b ) {
            return mb_strlen( $b ) - mb_strlen( $a );
        });

        foreach ( $this->get_file_info_structures( $directory_path, $filename_regex ) as $structure ) {
            $extension = '';
            $filename_with_flags = '';

            foreach ( $extensions as $ext ) {
                $basename = $structure[ 'basename' ];
                $basename_length = mb_strlen( $basename );
                $extension_length = mb_strlen( $ext );
                
                if ( mb_substr( $basename, $basename_length - $extension_length ) == $ext ) {
                    $extension = mb_substr( $ext, 1 );
                    $filename_with_flags = mb_substr( $basename, 0, $basename_length - mb_strlen( $extension ) - 1 );
                    break;
                }
            }

            $flags = explode( '.', $filename_with_flags );

            $filename_without_flags = $flags[0];

            // If the array contains one item, then no dots found in the file name, so no flags present. The one item 
            // returned is the file name.
            $flags = ( count( $flags ) == 1 ) ? array() : array_slice( $flags, 1 );

            $asset_name_rule = null;

            if ( isset( $asset_name_to_asset_name_rule[ $filename_without_flags ] ) ) {
                $asset_name_rule = $asset_name_to_asset_name_rule[ $filename_without_flags ];
            } else {
                foreach ( $asset_name_to_asset_name_rule as $asset_name => $instance ) {
                    if ( preg_match( "/^$asset_name$/", $filename_without_flags ) ) {
                        $asset_name_rule = $instance;
                    }
                }
            }

            // Normally every filename without flags should be available in the following associative array, as each 
            // filename found is linked to the asset names from which this associative array was built. However, if that 
            // is not the case, a bug should exist or who knows.
            if ( ! $asset_name_rule ) {
                throw new \Exception("File '$filename_without_flags' not applicable to requested assets with regex '$filename_regex'");
            }

            $assets[] = new Asset(
                $asset_type, 
                $extension, 
                $structure[ 'dirname' ] . DIRECTORY_SEPARATOR . $structure[ 'basename' ], 
                $filename_with_flags, 
                $asset_name_rule->get_context(),
                $asset_name_rule->get_langcode(),
                $flags
            );
        }

        return $assets;
    }

    public function get_assets_global( string $asset_type )
    {
        return $this->get_assets( $this->get_supported_asset_names_global(), $asset_type );
    }
    
    public function get_assets_for_object( $object, string $asset_type ) 
    {
        return $this->get_assets( $this->get_supported_asset_names_for_object( $object ), $asset_type );
    }

    public function get_supported_asset_names_for_object( $object )
    {
        $asset_name_rules_functions = array(
            array( $this, 'get_supported_asset_name_rules_if_term_object' ),
            array( $this, 'get_supported_asset_name_rules_if_post_object' ),
            array( $this, 'get_supported_asset_name_rules_if_user_object' ),
            array( $this, 'get_supported_asset_name_rules_if_archive' ),
            array( $this, 'get_supported_asset_name_rules_if_search' ),
            array( $this, 'get_supported_asset_name_rules_if_not_found' ),
        );

        return array_reduce($asset_name_rules_functions, function( $all, $fn ) use ( $object ) {
            $all = array_merge( $all, $fn( $object ) );
            return $all;
        }, array());
    }

    public function get_supported_asset_names_global()
    {
        return $this->get_language_augmented_asset_name_rules(array( 
            // new AssetNameRule( 'global(-[a-zA-Z0-1]+)?', 'global' ),
            new AssetNameRule( 'global', 'global' ),
        ));
    }

    private function get_default_language_object( $object ) 
    {
        if ( ! class_exists( 'SitePress' ) ) {
            return $object;
        }

        $default_language_object = $object;

        global $sitepress;
        $default_langcode = $sitepress->get_default_language();
        $current_langcode = $sitepress->get_current_language();

        if ( $object instanceof \WP_Term ) {
            $default_id = apply_filters( 'wpml_object_id', $object->term_id, $object->taxonomy, true, $default_langcode );
            
            if ( $default_id != $object->term_id ) {
                $sitepress->switch_lang( $default_langcode );
                $default_language_object = get_term( $default_id, $object->taxonomy );
                $sitepress->switch_lang( $current_langcode );
            }
        } else if ( $object instanceof \WP_Post ) {
            $default_id = apply_filters( 'wpml_object_id', $object->ID, $object->post_type, true, $default_langcode );
            
            if ( $default_id != $object->ID ) {
                $sitepress->switch_lang( $default_langcode );
                $default_language_object = get_post( $default_id, $object->post_type );
                $sitepress->switch_lang( $current_langcode );
            }
        }

        return $default_language_object;
    }

    public function get_supported_asset_name_rules_if_term_object( $object )
    {
        if ( ! $object instanceof \WP_Term ) {
            return array();
        }

        $default_language_object = $this->get_default_language_object( $object );

        $names = $this->get_language_augmented_asset_name_rules(array(
            new AssetNameRule( 'term' ),
            new AssetNameRule( 'term-slug-' . $default_language_object->slug ), 
            new AssetNameRule( 'term-id-' . $default_language_object->term_id ),
            new AssetNameRule( 'tax-' . $default_language_object->taxonomy ),
            new AssetNameRule( 'tax-' . $default_language_object->taxonomy . '-term-slug-' . $default_language_object->slug ),
            new AssetNameRule( 'tax-' . $default_language_object->taxonomy . '-term-id-' . $default_language_object->term_id ),
        ));

        if ( $object->term_id != $default_language_object->term_id ) {
            $names[] = new AssetNameRule( 'term-slug-' . $object->slug );
            $names[] = new AssetNameRule( 'term-id-' . $object->term_id );
            $names[] = new AssetNameRule( 'tax-' . $object->taxonomy . '-term-slug-' . $object->slug );
            $names[] = new AssetNameRule( 'tax-' . $object->taxonomy . '-term-id-' . $object->term_id );
        }

        return $names;
    }

    public function get_supported_asset_name_rules_if_post_object( $object )
    {
        if ( ! $object instanceof \WP_Post ) {
            return array();
        }

        $default_language_object = $this->get_default_language_object( $object );

        $names = $this->get_language_augmented_asset_name_rules(array(
            new AssetNameRule( 'type' ),
            new AssetNameRule( 'type-id-' . $default_language_object->ID ),
            new AssetNameRule( 'type-slug-' . $default_language_object->post_name ),
            new AssetNameRule( 'type-' . $default_language_object->post_type ),
            new AssetNameRule( 'type-' . $default_language_object->post_type . '-slug-' . $default_language_object->post_name ),
            new AssetNameRule( 'type-' . $default_language_object->post_type . '-id-' . $default_language_object->ID ),
        ));

        if ( $object->ID != $default_language_object->ID ) {
            $names[] = new AssetNameRule( 'type-id-' . $object->ID );
            $names[] = new AssetNameRule( 'type-slug-' . $object->post_name );
            $names[] = new AssetNameRule( 'type-' . $object->post_type . '-slug-' . $object->post_name );
            $names[] = new AssetNameRule( 'type-' . $object->post_type . '-id-' . $object->ID );
        }

        return $names;
    }

    public function get_supported_asset_name_rules_if_user_object( $object )
    {
        if ( ! $object instanceof \WP_User ) {
            return array();
        }

        $names = array(
            new AssetNameRule( 'user' ),
            new AssetNameRule( 'user-id-' . $object->data->ID ),
        );

        return $names;
    }

    public function get_supported_asset_name_rules_if_archive( $object )
    {
        if ( ! is_archive() ) {
            return array();
        }

        $names = array(
            new AssetNameRule( 'archive' ),
        );

        if ( is_date() ) {
            $names[] = new AssetNameRule( 'archive-date' );
        } else if ( $object instanceof \WP_Post_Type ) {
            $names[] = new AssetNameRule( 'archive-type-' . $object->name );
        }

        return $this->get_language_augmented_asset_name_rules( $names );
    }

    public function get_supported_asset_name_rules_if_search( $object )
    {
        if ( ! is_search() ) {
            return array();
        }

        return $this->get_language_augmented_asset_name_rules(array(
            new AssetNameRule( 'search' ),
        ));
    }

    public function get_supported_asset_name_rules_if_not_found( $object )
    {
        if ( ! is_404() ) {
            return array();
        }

        return $this->get_language_augmented_asset_name_rules(array(
            new AssetNameRule( 'not-found' ),
        ));
    }
}