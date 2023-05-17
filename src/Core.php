<?php

declare( strict_types=1 );

namespace panastasiadist\Enqueueror;

use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Base\Processor as Processor;
use panastasiadist\Enqueueror\Flags\Source as SourceFlag;
use panastasiadist\Enqueueror\Flags\Location as LocationFlag;
use panastasiadist\Enqueueror\Utilities\Htaccess as HtaccessUtility;
use panastasiadist\Enqueueror\Processors\JS\Raw as RawJsProcessor;
use panastasiadist\Enqueueror\Processors\JS\Php as PhpJsProcessor;
use panastasiadist\Enqueueror\Processors\CSS\Php as PhpCssProcessor;
use panastasiadist\Enqueueror\Processors\CSS\Raw as RawCssProcessor;

class Core {
	/**
	 * An array of supported processor classes.
	 *
	 * @var Processor[]
	 */
	const PROCESSORS = array(
		RawJsProcessor::class,
		PhpJsProcessor::class,
		PhpCssProcessor::class,
		RawCssProcessor::class,
	);

	/**
	 * A single Explorer instance. Initialized in the constructor.
	 *
	 * @var Explorer
	 */
	private $explorer;

	/**
	 * File extension mapped to the Processor class that handles it.
	 *
	 * @var array<string, Processor>
	 */
	private $extension_to_processor = array();

	/**
	 * Constructor
	 *
	 * @param string $plugin_file_path Plugin's main file path.
	 */
	public function __construct( string $plugin_file_path ) {
		$asset_type_to_config = array();

		// Each processor generates code for a specific type of asset recognized by one or more file extensions.

		foreach ( self::PROCESSORS as $class ) {
			$type = $class::get_supported_asset_type();

			foreach ( $class::get_supported_extensions() as $extension ) {
				$asset_type_to_config[ $type ]['extensions'][] = $extension;
				$this->extension_to_processor[ $extension ]    = $class;
			}
		}

		$base_directory_path = get_stylesheet_directory();

		foreach ( $asset_type_to_config as $asset_type => &$config ) {
			$config['directory_path'] = $base_directory_path . DIRECTORY_SEPARATOR . $asset_type;
		}

		$this->explorer = new Explorer( $asset_type_to_config );

		// Output assets in the <head> section of the HTML document.
		add_action( 'wp_enqueue_scripts', array( $this, 'output_head_assets' ) );

		// Output assets in the <body> section of the HTML document.
		add_action( 'wp_footer', array( $this, 'output_footer_assets' ) );

		// Update .htaccess with useful stuff when the active theme changes while the plugin is already active.
		add_action( 'switch_theme', array( $this, 'write_htaccess' ), 10, 0 );

		// Update .htaccess with useful stuff when the plugin is activated.
		register_activation_hook( $plugin_file_path, array( $this, 'write_htaccess' ) );

		// Clean .htaccess stuff when the plugin is deactivated.
		register_deactivation_hook( $plugin_file_path, array( HtaccessUtility::class, 'delete' ) );
	}

	/**
	 * Writes useful .htaccess rules, taking into account the current state of the system.
	 *
	 * @return void
	 */
	public function write_htaccess() {
		$base_directory_path = wp_get_theme()->get_stylesheet_directory();

		$paths = array_unique( array_map( function ( string $processor_class ) use ( $base_directory_path ) {
			return $base_directory_path . DIRECTORY_SEPARATOR . $processor_class::get_supported_asset_type();
		}, self::PROCESSORS ) );

		HtaccessUtility::write( $paths );
	}

	/**
	 * Coordinates the output of assets applicable to the current page.
	 *
	 * @param string $for_location Output assets designated only for the requested location.
	 * @param string[] $with_sources Output assets with the provided sources.
	 *
	 * @return void
	 */
	private function enqueue( string $for_location ) {
		/**
		 * A bag of discovered assets, accessed by their type.
		 *
		 * @var array<string, Asset[]> $asset_type_to_discovered_assets
		 */
		static $asset_type_to_discovered_assets = null;

		// This is the first time this function is run.
		// Call the Explorer to discover all assets handled by the available Processors.
		// The assets will be filtered and sorted out each time this function is called, according to the arguments.
		if ( null === $asset_type_to_discovered_assets ) {
			foreach ( array( 'scripts', 'stylesheets' ) as $type ) {
				$asset_type_to_discovered_assets[ $type ] = $this->explorer->get_assets( $type );
			}
		}

		foreach ( array( 'scripts', 'stylesheets' ) as $type ) {
			$assets = $asset_type_to_discovered_assets[ $type ];
			$assets = Manager::get_assets_filtered( $assets, $for_location );
			$assets = Manager::get_assets_sorted( $assets );

			$this->output_assets( $assets );
		}
	}

	/**
	 * Coordinates the output of the provided assets according to their type and their flags.
	 *
	 * @param Asset[] $assets The assets to handle their output in the page.
	 *
	 * @return void
	 */
	private function output_assets( array $assets ) {
		static $processed_asset_handles = array();

		foreach ( $assets as $asset ) {
			$handle = $asset->get_relative_filepath();

			// An asset may be encountered more than one times due to dependency resolution.
			// If the asset is already processed, then ignore it. All its dependencies are also already processed.
			if ( in_array( $handle, $processed_asset_handles ) ) {
				continue;
			}

			$file_path = $this->get_asset_serving_filepath( $asset );

			// No need to continue if unable to read the asset's contents.
			if ( false === $file_path ) {
				continue;
			}

			// Recursively explore any dependencies of the asset and get the handles to the direct dependencies.
			$dependencies = $this->handle_asset_dependencies( $asset, $processed_asset_handles );

			if ( SourceFlag::VALUE_EXTERNAL === $asset->get_flag( SourceFlag::get_name() ) ) {
				$file_url = $this->get_url_from_path( $file_path );

				if ( 'scripts' === $asset->get_type() ) {
					wp_enqueue_script( $handle, $file_url, $dependencies, filemtime( $file_path ) );
				} else {
					// stylesheets
					wp_enqueue_style( $handle, $file_url, $dependencies, filemtime( $file_path ) );
				}
			} else {
				// SourceFlag = internal
				if ( 'scripts' === $asset->get_type() ) {
					wp_register_script( $handle, '', $dependencies );
					wp_enqueue_script( $handle );
					wp_add_inline_script( $handle, file_get_contents( $file_path ) );
				} else {
					// stylesheets
					wp_register_style( $handle, false, $dependencies );
					wp_enqueue_style( $handle );
					wp_add_inline_style( $handle, file_get_contents( $file_path ) );
				}
			}

			$processed_asset_handles[] = $handle;
		}
	}

	/**
	 * Prepares an asset's dependencies to be pushed to the browser and returns the relevant handles.
	 *
	 * @param Asset $asset An Asset instance to load its dependencies.
	 * @param array $processed_asset_handles An array storing the handles of processed resources
	 *
	 * @return array An array of handles of the direct dependencies of the provided asset.
	 */
	private function handle_asset_dependencies( Asset $asset, array &$processed_asset_handles ): array {
		$dependencies        = array();
		$dependencies_urls   = array();
		$dependencies_assets = array();

		foreach ( $this->get_asset_dependencies( $asset ) as $dependency ) {
			$dependencies[] = $dependency;

			$http = 0 === mb_strpos( $dependency, 'http://' ) || 0 === mb_strpos( $dependency, 'https://' );

			if ( $http && filter_var( $dependency, FILTER_VALIDATE_URL ) ) {
				$dependencies_urls[] = $dependency;
			} else if ( 0 === mb_strpos( $dependency, '/' ) ) {
				// Check if the dependency is an asset and act accordingly.
				$dependencies_assets[] = $this->explorer->get_asset_for_file_path( $dependency, $asset->get_type() );
			}
		}

		$this->output_assets( $dependencies_assets );

		foreach ( $dependencies_urls as $dependency_url ) {
			if ( in_array( $dependency_url, $processed_asset_handles ) ) {
				continue;
			}

			if ( 'scripts' === $asset->get_type() ) {
				wp_enqueue_script( $dependency_url, $dependency_url );
			} else {
				// stylesheets
				wp_enqueue_style( $dependency_url, $dependency_url );
			}

			$processed_asset_handles[] = $dependency_url;
		}

		return $dependencies;
	}

	/**
	 * Returns an array of dependencies specified by the provided asset.
	 *
	 * @param Asset $asset An instance of an asset to return its dependencies.
	 *
	 * @return string[] Array of dependencies.
	 */
	private function get_asset_dependencies( Asset $asset ): array {
		$dependencies = array();

		$processor_class = $this->extension_to_processor[ $asset->get_extension() ];

		$header = $processor_class::get_header_values( $asset->get_absolute_filepath() );

		if ( $header && isset( $header['Requires'] ) ) {
			$dependencies = explode( ',', $header['Requires'] );
			$dependencies = array_map( 'trim', $dependencies );
			$dependencies = array_filter( $dependencies, function ( $dependency ) {
				return $dependency != '';
			} );
		}

		return $dependencies;
	}

	/**
	 * Returns a path for an asset which can be used to serve the asset's contents in a format appropriate for browsers:
	 * - Returns the path of the original asset file, if the latter does not require processing before being served.
	 * - Alternatively, returns the path of a processed version of the original asset which is appropriate for browsers.
	 *
	 * @param Asset $asset An instance of an asset to a return a path for.
	 *
	 * @return false|string The final filepath to be used for serving the asset to browsers or false on error
	 */
	private function get_asset_serving_filepath( Asset $asset ) {
		$processor_class = $this->extension_to_processor[ $asset->get_extension() ];

		$processed_file_path = $processor_class::get_processed_filepath( $asset->get_absolute_filepath() );

		if ( false === $processed_file_path ) {
			return false;
		}

		if ( $processed_file_path !== $asset->get_absolute_filepath() ) {
			$store_extension = $asset->get_type() === 'scripts' ? 'js' : 'css';

			$processed_file_path = $this->store_processed_file(
				$processed_file_path,
				$asset->get_relative_filepath(),
				$store_extension
			);
		}

		return $processed_file_path;
	}

	/**
	 * Stores a processed file in the public directory and returns its path.
	 *
	 * @param string $processed_file_path The current path to the processed file to store in the public directory.
	 * @param string $source_basename The basename of the source file from which the processed file derives its content.
	 * @param string $extension The extension to use for the final processed file when stored in the public directory.
	 *
	 * @return false|string The file path to the final processed file in the public directory.
	 */
	private function store_processed_file( string $processed_file_path, string $source_basename, string $extension ) {
		// The root directory processed files are stored under.
		$serving_directory_path = WP_CONTENT_DIR . '/uploads/enqueueror';

		// If the source file basename contains slashes, then the source file's name represents its path relatively to its root directory.
		// Make sure the directory structure of the source file is represented within the serving directory.
		$source_basename_parts = explode( '/', $source_basename );

		// Clean the empty parts of the basename, produced by a basename beginning and/or ending with a slash (/).
		$source_basename_parts = array_filter( $source_basename_parts, function ( $part ) {
			return '' !== $part;
		} );

		// If the parts of the cleaned parts are more than one, then slashes were found between the first and last character of the basename.
		// As a result, the basename truly represents a directory hierarchy the source file is located under.
		// This directory structure should be reproduced within the serving directory.
		// All parts but the last one are nested directories to be created under the serving directory.
		// The last part is the actual basename of the source file.
		// Append the directory parts to the root serving directory path to form the final serving directory path of the processed file.
		if ( count( $source_basename_parts ) > 1 ) {
			$serving_directory_path .= DIRECTORY_SEPARATOR . implode( DIRECTORY_SEPARATOR, array_slice( $source_basename_parts, 0, - 1 ) );
		}

		// Try creating the final serving directory.
		if ( false === wp_mkdir_p( $serving_directory_path ) ) {
			return false;
		}

		// The last one of the parts will always be the actual basename of the source file.
		$source_basename = end( $source_basename_parts );

		// The processed file will be stored with a basename of the following pattern "$source_file_basename[HASH_SEPARATOR][HASH].$extension"
		// The [HASH] part is calculated according to the contents of the processed file.
		// The serving directory may contain out-of-date processed files that should be deleted for cleanup purposes.
		// The serving directory should contain only the processed file corresponding to the content of the source file.

		$hash_separator = '-';

		static $source_basename_to_processed_file_paths = null;

		if ( null === $source_basename_to_processed_file_paths ) {
			// Search in the serving directory for processed files and group their paths by the basename of the source file.
			foreach ( glob( "$serving_directory_path/*.$extension" ) as $processed_file_path ) {
				$basename              = basename( $processed_file_path );
				$basename_parts        = explode( $hash_separator, $basename );
				$name_part             = implode( $hash_separator, array_slice( $basename_parts, 0, - 1 ) );
				$basename_without_hash = $name_part . $extension;

				// The associative array will be used in the next stage to remove any out-of-date processed files.
				$source_basename_to_processed_file_paths[ $basename_without_hash ][] = $serving_directory_path . DIRECTORY_SEPARATOR . $basename;
			}
		}

		// Calculate according the processed file's content, itself originating from the source file's content.
		// As a result, the hash will be "unique" to the content of the source file.
		$hash = md5_file( $processed_file_path );

		$target_file_path = $serving_directory_path . DIRECTORY_SEPARATOR . $source_basename . $hash_separator . $hash . '.' . $extension;

		if ( ! file_exists( $target_file_path ) ) {
			$basename_without_hash = $source_basename . $extension;

			// We are here, because the path of the new processed file does not yet exist.
			// Time to clean any out-of-date processed files stored in the serving directory originating from the same file.
			if ( isset( $source_basename_to_processed_file_paths[ $basename_without_hash ] ) ) {
				foreach ( $source_basename_to_processed_file_paths[ $basename_without_hash ] as $expired_asset_filepath ) {
					unlink( $expired_asset_filepath );
				}
			}

			// Now copy the processed file from its temporary location to its final destination.
			copy( $processed_file_path, $target_file_path );

			// The newly created processed file is now linked to the file it derives from.
			$source_basename_to_processed_file_paths[ $basename_without_hash ][] = $target_file_path;
		}

		return $target_file_path;
	}

	/**
	 * Translates an absolute filesystem path to its equivalent public URL.
	 *
	 * @param string $path The filesystem path.
	 *
	 * @return string The absolute public URL.
	 */
	private function get_url_from_path( string $path ): string {
		$url = str_replace(
			wp_normalize_path( untrailingslashit( ABSPATH ) ),
			site_url(),
			wp_normalize_path( $path )
		);

		return esc_url_raw( $url );
	}

	/**
	 * Fires the output of assets that should be loaded in the <head> section of the HTML document.
	 *
	 * @return void
	 */
	public function output_head_assets() {
		$this->enqueue( LocationFlag::VALUE_HEAD );
	}

	/**
	 * Fires the output of assets that should be loaded before the </body> tag of the HTML document.
	 *
	 * @return void
	 */
	public function output_footer_assets() {
		$this->enqueue( LocationFlag::VALUE_FOOTER );
	}
}