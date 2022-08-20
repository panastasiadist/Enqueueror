<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Base\Descriptor;

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

    private function get_language_augmented_descriptors( array $descriptors )
    {
        if ( ! class_exists( 'SitePress' ) ) {
            return $descriptors;
        }

        global $sitepress;
            
        $current_langcode = $sitepress->get_current_language();

        if ( ! $current_langcode ) {
            return $descriptors;
        }

        $descriptors = array_merge($descriptors, array_map(function( $descriptor ) use ( $current_langcode ) {
            return new Descriptor(
	            $descriptor->get_pattern() . '-' . $current_langcode,
                $descriptor->get_context(),
                $current_langcode
            );
        }, $descriptors));

        return $descriptors;
    }

    public function __construct( string $assets_directory_path )
    {
        $this->asset_type_to_directory_path[ 'scripts' ] = 
            $assets_directory_path . DIRECTORY_SEPARATOR . self::SCRIPTS_DIRECTORY_NAME;
        
        $this->asset_type_to_directory_path[ 'stylesheets' ] = 
            $assets_directory_path . DIRECTORY_SEPARATOR . self::STYLESHEETS_DIRECTORY_NAME;
    }

    public function get_asset_directory_paths()
    {
        return $this->asset_type_to_directory_path;
    }

    public function register_asset_extensions( string $asset_type, array $extensions )
    {
        if ( isset( $this->asset_type_to_supported_extensions[ $asset_type ] ) ) {
            $this->asset_type_to_supported_extensions[ $asset_type ] = $extensions;
        } else {
            throw new \Exception( "Extensions for asset type '$asset_type' are not supported" );
        }
    }

    private function get_asset_for_file_info_structure(
        array $structure, 
        string $asset_type, 
        string $directory_path, 
        array $extensions)
    {
        $extensions = array_map(function($extension) {
            return '.' !== mb_substr( $extension, 0, 1 ) ? '.' . $extension : $extension;
        }, $extensions);

        // Sort the extensions, so the more lengthy come first.
        usort($extensions, function( $a, $b ) {
            return mb_strlen( $b ) - mb_strlen( $a );
        });

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

        $langcode = 'all';

        if ( class_exists( 'SitePress' ) ) {
            global $sitepress;
            
            $current_langcode = $sitepress->get_current_language();

            if ( $current_langcode ) {
                $langcode_suffix = '-' . $current_langcode;
                $langcode_suffix_length = mb_strlen( $langcode_suffix );
                if ( $langcode_suffix === mb_substr( $filename_without_flags, -$langcode_suffix_length ) ) {
                    $langcode = $current_langcode;
                }
            }
        }

        $context = 'current';

        if ( 'global' === $filename_without_flags ) {
            $context = 'global';
        }

        $absolute_filepath = $structure[ 'dirname' ] . DIRECTORY_SEPARATOR . $structure[ 'basename' ];
        $relative_filepath = str_replace( $directory_path, '', $absolute_filepath );

        return new Asset(
            $asset_type,
            $extension,
            $absolute_filepath,
            $relative_filepath,
            $filename_with_flags,
            $context,
            $langcode,
            $flags
        );
    }

    /**
     * Searches the filesystem for available asset files returning an array of Asset instances.
     *
     * @param Descriptor[] $descriptors An array of Descriptor instances about which assets to return.
     * @param string $asset_type A string representing the type that the found asset files are designated for. 
     * Valid values are 'scripts' and 'stylesheets'.
     * @return Asset[] An array of Asset instances representing all found asset files. 
     */
    private function get_assets( array $descriptors, string $asset_type )
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

        $descriptors = array_map(function( $descriptor ) {
            return $descriptor->get_pattern();
        }, $descriptors);

        $filenames_regex = implode( '|', $descriptors );
        $filenames_regex = str_replace( '.', '\.', $filenames_regex );
        $filename_regex = "/^($filenames_regex)(\.[a-zA-Z0-9\-_\.]*)?($extension_regex)$/";

        $assets = array();

        foreach ( $this->get_file_info_structures( $directory_path, $filename_regex ) as $structure ) {
            $assets[] = $this->get_asset_for_file_info_structure( $structure, $asset_type, $directory_path, $extensions );
        }

        return $assets;
    }

    /**
     * Returns an Asset instance given an asset's filesystem path within the directory setup for the given asset type.
     *
     * @param string $filepath The absolute/relative filesystem path to an asset. If a relative path is given, then the 
     * path must start with a '/' and it must be relative to the directory containing assets of type $asset_type.
     * @param string $asset_type The type of the asset designated by the filesystem path given.
     * @return Asset The Asset instance for the requested asset or false on failure. 
     * @throws Exception Thrown if $asset_type is not supported.
     */
    public function get_asset_for_filepath( string $filepath, string $asset_type )
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

        $filepath = $directory_path . $filepath;

        $structure = pathinfo( $filepath );

        return $this->get_asset_for_file_info_structure( $structure, $asset_type, $directory_path, $extensions );
    }

    public function get_assets_global( string $asset_type )
    {
        return $this->get_assets( $this->get_supported_descriptors_global(), $asset_type );
    }
    
    public function get_assets_for_object( $object, string $asset_type ) 
    {
        return $this->get_assets( $this->get_supported_descriptors_for_object( $object ), $asset_type );
    }

    public function get_supported_descriptors_for_object( $object )
    {
        $descriptor_provider_functions = array(
            array( $this, 'get_supported_descriptors_if_term_object' ),
            array( $this, 'get_supported_descriptors_if_post_object' ),
            array( $this, 'get_supported_descriptors_if_user_object' ),
            array( $this, 'get_supported_descriptors_if_archive' ),
            array( $this, 'get_supported_descriptors_if_search' ),
            array( $this, 'get_supported_descriptors_if_not_found' ),
        );

        return array_reduce($descriptor_provider_functions, function( $all, $fn ) use ( $object ) {
            return array_merge( $all, $fn( $object ) );
        }, array());
    }

    public function get_supported_descriptors_global()
    {
        return $this->get_language_augmented_descriptors(array(
            // new Descriptor( 'global(-[a-zA-Z0-1]+)?', 'global' ),
            new Descriptor( 'global', 'global' ),
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

        if ( ! ( $default_langcode && $current_langcode ) ) {
            return $default_language_object;
        }

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

    public function get_supported_descriptors_if_term_object( $object )
    {
        if ( ! $object instanceof \WP_Term ) {
            return array();
        }

        $default_language_object = $this->get_default_language_object( $object );

        $descriptors = $this->get_language_augmented_descriptors(array(
            new Descriptor( 'term' ),
            new Descriptor( 'term-slug-' . $default_language_object->slug ),
            new Descriptor( 'term-id-' . $default_language_object->term_id ),
            new Descriptor( 'tax-' . $default_language_object->taxonomy ),
            new Descriptor( 'tax-' . $default_language_object->taxonomy . '-term-slug-' . $default_language_object->slug ),
            new Descriptor( 'tax-' . $default_language_object->taxonomy . '-term-id-' . $default_language_object->term_id ),
        ));

        if ( $object->term_id != $default_language_object->term_id ) {
            $descriptors[] = new Descriptor( 'term-slug-' . $object->slug );
            $descriptors[] = new Descriptor( 'term-id-' . $object->term_id );
            $descriptors[] = new Descriptor( 'tax-' . $object->taxonomy . '-term-slug-' . $object->slug );
            $descriptors[] = new Descriptor( 'tax-' . $object->taxonomy . '-term-id-' . $object->term_id );
        }

        return $descriptors;
    }

    public function get_supported_descriptors_if_post_object( $object )
    {
        if ( ! $object instanceof \WP_Post ) {
            return array();
        }

        $default_language_object = $this->get_default_language_object( $object );

        $descriptors = $this->get_language_augmented_descriptors(array(
            new Descriptor( 'type' ),
            new Descriptor( 'type-id-' . $default_language_object->ID ),
            new Descriptor( 'type-slug-' . $default_language_object->post_name ),
            new Descriptor( 'type-' . $default_language_object->post_type ),
            new Descriptor( 'type-' . $default_language_object->post_type . '-slug-' . $default_language_object->post_name ),
            new Descriptor( 'type-' . $default_language_object->post_type . '-id-' . $default_language_object->ID ),
        ));

        if ( $object->ID != $default_language_object->ID ) {
            $descriptors[] = new Descriptor( 'type-id-' . $object->ID );
            $descriptors[] = new Descriptor( 'type-slug-' . $object->post_name );
            $descriptors[] = new Descriptor( 'type-' . $object->post_type . '-slug-' . $object->post_name );
            $descriptors[] = new Descriptor( 'type-' . $object->post_type . '-id-' . $object->ID );
        }

        return $descriptors;
    }

    public function get_supported_descriptors_if_user_object( $object )
    {
        if ( ! $object instanceof \WP_User ) {
            return array();
        }

        return array(
            new Descriptor( 'user' ),
            new Descriptor( 'user-id-' . $object->data->ID ),
        );
    }

    public function get_supported_descriptors_if_archive( $object )
    {
        if ( ! is_archive() ) {
            return array();
        }

        $descriptors = array(
            new Descriptor( 'archive' ),
        );

        if ( is_date() ) {
            $descriptors[] = new Descriptor( 'archive-date' );
        } else if ( $object instanceof \WP_Post_Type ) {
            $descriptors[] = new Descriptor( 'archive-type-' . $object->name );
        }

        return $this->get_language_augmented_descriptors( $descriptors );
    }

    public function get_supported_descriptors_if_search( $object )
    {
        if ( ! is_search() ) {
            return array();
        }

        return $this->get_language_augmented_descriptors(array(
            new Descriptor( 'search' ),
        ));
    }

    public function get_supported_descriptors_if_not_found( $object )
    {
        if ( ! is_404() ) {
            return array();
        }

        return $this->get_language_augmented_descriptors(array(
            new Descriptor( 'not-found' ),
        ));
    }
}