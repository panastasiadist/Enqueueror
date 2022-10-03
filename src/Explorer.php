<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror;

use Exception;
use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Base\Descriptor;
use panastasiadist\Enqueueror\Descriptors\Archive;
use panastasiadist\Enqueueror\Descriptors\Generic;
use panastasiadist\Enqueueror\Descriptors\NotFound;
use panastasiadist\Enqueueror\Descriptors\Post;
use panastasiadist\Enqueueror\Descriptors\Search;
use panastasiadist\Enqueueror\Descriptors\Term;
use panastasiadist\Enqueueror\Descriptors\User;

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

	/**
	 * @param string $assets_directory_path A filesystem path to a directory to search for asset files in.
	 */
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
            throw new Exception( "Extensions for asset type '$asset_type' are not supported" );
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
     * @param Description[] $descriptors An array of Descriptor instances about which assets to return.
     * @param string $asset_type A string representing the type that the found asset files are designated for. 
     * Valid values are 'scripts' and 'stylesheets'.
     *
     * @return Asset[] An array of Asset instances representing all found asset files.
     */
    private function get_assets_by_descriptions( array $descriptors, string $asset_type )
    {
        if ( isset( $this->asset_type_to_directory_path[ $asset_type ] ) ) {
            $directory_path = $this->asset_type_to_directory_path[ $asset_type ];
        } else {
            throw new Exception( "No asset directory registered for '$asset_type' asset type" );
        }

        if ( isset( $this->asset_type_to_supported_extensions[ $asset_type ] ) ) {
            $extensions = $this->asset_type_to_supported_extensions[ $asset_type ];
        } else {
            throw new Exception( "No extensions registered for '$asset_type' asset type" );
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
            throw new Exception( "No asset directory registered for '$asset_type' asset type" );
        }

        if ( isset( $this->asset_type_to_supported_extensions[ $asset_type ] ) ) {
            $extensions = $this->asset_type_to_supported_extensions[ $asset_type ];
        } else {
            throw new Exception( "No extensions registered for '$asset_type' asset type" );
        }

        $filepath = $directory_path . $filepath;

        $structure = pathinfo( $filepath );

        return $this->get_asset_for_file_info_structure( $structure, $asset_type, $directory_path, $extensions );
    }

	/**
	 * Returns an array of Asset instances corresponding to the current request and asset type.
	 *
	 * @param string $asset_type The type of Assets to return.
	 * @return Asset[] An array of Asset instances applicable to the current request.
	 * @throws Exception If unable to discover assets applicable to the current request.
	 */
	public function get_assets( string $asset_type ): array
	{
		/**
		 * @var Descriptor[] $descriptors An array of Descriptor classes to use.
		 */
		$descriptors = array(
			Archive::class,
			Generic::class,
			NotFound::class,
			Post::class,
			Search::class,
			Term::class,
			User::class,
		);

		$descriptions = array();

		foreach ( $descriptors as $descriptor ) {
			$descriptions = array_merge( $descriptions, $descriptor::get() );
		}

		return $this->get_assets_by_descriptions( $descriptions, $asset_type );
	}
}