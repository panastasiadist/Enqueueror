<?php
declare( strict_types=1 );

namespace panastasiadist\Enqueueror;

use Exception;
use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Base\Description;
use panastasiadist\Enqueueror\Descriptors\Archive;
use panastasiadist\Enqueueror\Descriptors\Generic;
use panastasiadist\Enqueueror\Descriptors\NotFound;
use panastasiadist\Enqueueror\Descriptors\Post;
use panastasiadist\Enqueueror\Descriptors\Search;
use panastasiadist\Enqueueror\Descriptors\Term;
use panastasiadist\Enqueueror\Descriptors\User;
use panastasiadist\Enqueueror\Utilities\Filesystem;
use panastasiadist\Enqueueror\Flags\Source as SourceFlag;
use panastasiadist\Enqueueror\Flags\Location as LocationFlag;

class Explorer {
	/**
	 * Array of Descriptors used by this class to discover Assets applicable to the request.
	 */
	const DESCRIPTORS = array(
		Archive::class,
		Generic::class,
		NotFound::class,
		Post::class,
		Search::class,
		Term::class,
		User::class,
	);

	/**
	 * @var array Associative array: string -> string. Asset type to absolute filesystem directory path.
	 */
	private $asset_type_to_directory_path = array();

	/**
	 * @var array Associative array: string -> string[]. Asset type to file extensions.
	 */
	private $asset_type_to_extensions = array();

	/**
	 * @var array Associative array: string -> string. Asset extension to asset type.
	 */
	private $extension_to_asset_type = array();

	/**
	 * Returns the absolute filesystem path to a directory configured for asset files of the provided asset type.
	 *
	 * @param string $asset_type An asset type to return its associated directory.
	 *
	 * @return string The absolute filesystem path.
	 * @throws Exception If a directory path hasn't been configured for the provided asset type.
	 */
	private function get_directory_path_for_asset_type( string $asset_type ): string {
		if ( isset( $this->asset_type_to_directory_path[ $asset_type ] ) ) {
			return $this->asset_type_to_directory_path[ $asset_type ];
		}

		throw new Exception( "No asset directory registered for '$asset_type' asset type" );
	}

	/**
	 * Returns the file extensions supported for asset files of the provided asset type.
	 *
	 * @param string $asset_type An asset type to return its supported file extensions.
	 *
	 * @return string[] The array of supported file extensions.
	 * @throws Exception If supported file extensions haven't been configured for the provided asset type.
	 */
	private function get_extensions_for_asset_type( string $asset_type ): array {
		if ( isset( $this->asset_type_to_extensions[ $asset_type ] ) ) {
			return $this->asset_type_to_extensions[ $asset_type ];
		}

		throw new Exception( "No extensions registered for '$asset_type' asset type" );
	}

	/**
	 * Returns an Asset instance representing the provided file.
	 *
	 * @param array $file An associative array containing information about a file in the filesystem.
	 *
	 * @return Asset The Asset instance representing the provided file.
	 * @throws Exception If the provided file is not supported.
	 */
	private function get_asset_for_file( array $file ): Asset {
		$extensions = array_keys( $this->extension_to_asset_type );

		// Sort the extensions, so the more lengthy come first.
		usort( $extensions, function ( string $a, string $b ) {
			return mb_strlen( $b ) - mb_strlen( $a );
		} );

		foreach ( $extensions as $extension ) {
			$basename_length      = mb_strlen( $file['basename'] );
			$dot_extension        = '.' . $extension;
			$dot_extension_length = mb_strlen( $dot_extension );

			if ( mb_substr( $file['basename'], $basename_length - $dot_extension_length ) == $dot_extension ) {
				$asset_type          = $this->extension_to_asset_type[ $extension ];
				$directory_path      = $this->get_directory_path_for_asset_type( $asset_type );
				$filename_with_flags = mb_substr( $file['basename'], 0, $basename_length - $dot_extension_length );
				break;
			}
		}

		if ( ! isset( $filename_with_flags ) ) {
			throw new Exception( "File '{$file[ 'basename' ]}' doesn't have a supported extension" );
		}

		$flag_values = explode( '.', $filename_with_flags );

		$filename_without_flags = $flag_values[0];

		// If the array contains one item, then no dots found in the filename, so no flags are present. The only item
		// returned is the filename.
		$flag_values = ( count( $flag_values ) == 1 ) ? array() : array_slice( $flag_values, 1 );

		$flags = array(
			SourceFlag::get_name()   => SourceFlag::get_detected_value( $flag_values ),
			LocationFlag::get_name() => LocationFlag::get_detected_value( $flag_values ),
		);

		$language_code = 'all';

		if ( $current_language_code = apply_filters( 'wpml_current_language', null ) ) {
			$language_code_suffix        = '-' . $current_language_code;
			$language_code_suffix_length = mb_strlen( $language_code_suffix );

			if ( $language_code_suffix === mb_substr( $filename_without_flags, - $language_code_suffix_length ) ) {
				$language_code = $current_language_code;
			}
		}

		$absolute_filepath = $file['dirname'] . DIRECTORY_SEPARATOR . $file['basename'];

		// $asset_type, $extension and $directory_path will actually be defined

		/** @noinspection PhpUndefinedVariableInspection */
		return new Asset(
			$asset_type,
			$extension,
			$absolute_filepath,
			str_replace( $directory_path, '', $absolute_filepath ),
			$filename_with_flags,
			'global' === $filename_without_flags ? 'global' : 'current',
			$language_code,
			$flags
		);
	}

	/**
	 * Searches the filesystem for available asset files and returns an array of associated Asset instances.
	 *
	 * @param Description[] $descriptions An array of Description instances regarding which assets to return.
	 * @param string $asset_type A string representing the type of assets to return given the Description instances.
	 *
	 * @return Asset[] An array of Asset instances representing all asset files found.
	 * @throws Exception If the provided asset type is not configured.
	 */
	private function get_assets_by_descriptions( array $descriptions, string $asset_type ): array {
		// Get the extensions supported for the provided asset type prepending them with a dot character as required in
		// the next stages when constructing the required regular expressions.
		$extensions = array_map( function ( $extension ) {
			return '.' . $extension;
		}, $this->get_extensions_for_asset_type( $asset_type ) );

		// The dot character has special meaning when used in the context of a regex.
		// Extensions are used as part of the regex, so their dot character should be escaped.
		$extension_regex = str_replace( '.', '\.', implode( '|', $extensions ) );

		$patterns = array_map( function ( Description $description ) {
			return $description->get_pattern();
		}, $descriptions );

		$filenames_regex = implode( '|', $patterns );
		$filenames_regex = str_replace( '.', '\.', $filenames_regex );
		$filename_regex  = "/^($filenames_regex)(\.[a-zA-Z0-9\-_\.]*)?($extension_regex)$/";

		$directory_path = $this->get_directory_path_for_asset_type( $asset_type );

		return array_map( function ( $file ) use ( $directory_path, $filename_regex ) {
			return $this->get_asset_for_file( $file );
		}, Filesystem::get_files( $directory_path, $filename_regex ) );
	}

	/**
	 * Constructs an instance of the Explorer class which locates asset files in the filesystem according to the
	 * provided configuration.
	 *
	 * @param array $asset_type_to_config An associative configuration array
	 */
	public function __construct( array $asset_type_to_config ) {
		foreach ( $asset_type_to_config as $asset_type => $config ) {
			foreach ( $config as $key => $value ) {
				if ( 'directory_path' == $key ) {
					$this->asset_type_to_directory_path[ $asset_type ] = $value;
				} else if ( 'extensions' == $key ) {
					$this->asset_type_to_extensions[ $asset_type ] = $value;

					foreach ( $value as $extension ) {
						$this->extension_to_asset_type[ $extension ] = $asset_type;
					}
				}
			}
		}
	}

	/**
	 * Returns an Asset instance given an asset's filesystem path within the directory setup for the given asset type.
	 *
	 * @param string $file_path The absolute/relative filesystem path to an asset. If a relative path is given, then the
	 * path must start with a '/' and it must be relative to the directory containing assets of type $asset_type.
	 * @param string $asset_type The type of the asset designated by the filesystem path given.
	 *
	 * @return Asset The Asset instance for the requested asset or false on failure.
	 * @throws Exception Thrown if $asset_type is not supported.
	 */
	public function get_asset_for_file_path( string $file_path, string $asset_type ): Asset {
		if ( realpath( $file_path ) === $file_path ) {
			$path = $file_path;
		} else {
			$path = $this->get_directory_path_for_asset_type( $asset_type ) . $file_path;
		}

		return $this->get_asset_for_file( pathinfo( $path ) );
	}

	/**
	 * Returns an array of Asset instances corresponding to the current request and the provided asset type.
	 *
	 * @param string $asset_type The type of Assets to return.
	 *
	 * @return Asset[] An array of Asset instances applicable to the current request.
	 * @throws Exception If unable to search for assets.
	 */
	public function get_assets( string $asset_type ): array {
		$descriptions = array_reduce( self::DESCRIPTORS, function ( array $acc, string $descriptor ) {
			return array_merge( $acc, $descriptor::get() );
		}, array() );

		return $this->get_assets_by_descriptions( $descriptions, $asset_type );
	}
}