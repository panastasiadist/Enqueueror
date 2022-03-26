<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror;

use __PHP_Incomplete_Class;
use panastasiadist\Enqueueror\Base\Asset;
use panastasiadist\Enqueueror\Flags\Source as SourceFlag;
use panastasiadist\Enqueueror\Flags\Location as LocationFlag;

class Core
{
    const PROCESSORS = array(
        __NAMESPACE__ . '\Processors\JS\Raw',
        __NAMESPACE__ . '\Processors\JS\Php',
        __NAMESPACE__ . '\Processors\CSS\Raw',
        __NAMESPACE__ . '\Processors\CSS\Php',
    );

    private $explorer = null;
    // private $logger = null;

    /**
     * Each supported asset file's extension corresponds to exactly one type of asset (script or stylesheet) and may be
     * handled by exactly one processor supporting the specified extension and the asset type designated by the latter.
     */
    private $extension_to_processor = array();

    public function __construct()
    {
        $type_to_extensions = array();

        // Each processor supports generating code for exactly one asset type (script or stylesheet) based on the code
        // contained in specific extensions supported by the processor.

        foreach ( self::PROCESSORS as $class ) {
            $type = $class::get_supported_asset_type();
            $extensions = $class::get_supported_extensions();

            if ( ! isset( $type_to_extensions[ $type ] ) ) {
                $type_to_extensions[ $type ] = array();
            }

            $type_to_extensions[ $type ] = array_merge( $type_to_extensions[ $type ], $extensions );

            foreach ( $extensions as $extension ) {
                $this->extension_to_processor[ $extension ] = $class;
            }
        }

        $this->explorer = new Explorer( get_stylesheet_directory() );
        // $this->logger = new Logger( __DIR__ . '/log.txt' );

        foreach ( $type_to_extensions as $type => $extensions ) {
            $this->explorer->register_asset_extensions( $type, $extensions );
        }

        add_action( 'wp_enqueue_scripts', array( $this, 'output_enqueueable' ) );
        add_action( 'wp_head', array( $this, 'output_head_printed' ) );
        add_action( 'get_footer', array( $this, 'output_footer' ) );
    }

    /**
     * Coordinates the output of assets, applicable to the current page, taking into account the location and output 
     * modes.
     * 
     * @param string $for_location Output assets designated only for the requested location.
     * @param string[] $output_modes Output assets provided they are supported by the requested output modes.
     * @return void
     */
    private function enqueue( string $for_location, array $output_modes ) 
    {
        $queried_object = get_queried_object();

        foreach ( array( 'scripts', 'stylesheets' ) as $type ) {
            $assets = array_merge(
                $this->explorer->get_assets_global( $type ),
                $this->explorer->get_assets_for_object( $queried_object, $type )
            );

            $assets = Manager::get_assets_filtered( $assets, $for_location, $output_modes );
            $assets = Manager::get_assets_sorted( $assets );

            $this->output_assets( $assets );
        }
    }

    /**
     * Coordinates the enqueueing / output of the passed-in assets according to their type and flags.
     *
     * @param Asset[] $assets The assets to handle their output in the page.
     * @return void
     */
    private function output_assets( $assets )
    {
        static $enqueued_asset_handles = null;

        if ( null === $enqueued_asset_handles ) {
            $enqueued_asset_handles = array();
        }

        foreach ( $assets as $asset ) {
            $type = $asset->get_type();
            $source = SourceFlag::get_value_for_asset( $asset, 'external' );
            $location = LocationFlag::get_value_for_asset( $asset, 'head' );

            if ( 'external' == $source ) {
                $handle = $asset->get_relative_filepath();
                $file_path = $this->get_processed_asset_filepath( $asset, $handle );

                if ( false === $file_path ) {
                    continue;
                }

                $file_url = $this->get_url_from_path( $file_path );

                $dependencies = array();
                $dependencies_assets = array();
                $dependencies_urls = array();

                foreach ( $this->get_asset_dependencies( $asset ) as $dependency ) {
                    $dependencies[] = $dependency;

                    $http = 0 === mb_strpos( $dependency, 'http://' ) || 0 === mb_strpos( $dependency, 'https://' );

                    if ( $http && filter_var( $dependency, FILTER_VALIDATE_URL ) ) {
                        $dependencies_urls[] = $dependency;
                    } else if ( 0 === mb_strpos( $dependency, '/' ) ) {
                        // Check if the dependency is an asset and act accordingly.
                        $dependency_asset = $this->explorer->get_asset_for_filepath( $dependency, $type );
                        $dependency_asset_source = SourceFlag::get_value_for_asset( $dependency_asset, 'external' );

                        if ( 'external' == $dependency_asset_source ) {
                            // Only external assets are supported, so they are able to be enqueued and be part of WordPress dependency resolution.
                            $dependencies_assets[] = $dependency_asset;
                        }
                    }
                }

                $this->output_assets( $dependencies_assets );

                foreach ( $dependencies_urls as $dependency_url ) {
                    if ( ! in_array( $dependency_url, $enqueued_asset_handles ) ) {
                        if ( 'scripts' == $type ) {
                            wp_enqueue_script( $dependency_url, $dependency_url, array(), false, 'footer' == $location );
                            $enqueued_asset_handles[] = $dependency_url;
                        } elseif ( 'stylesheets' == $type ) {
                            wp_enqueue_style( $dependency_url, $dependency_url );
                            $enqueued_asset_handles[] = $dependency_url;
                        }
                    }
                }

                if ( ! in_array( $handle, $enqueued_asset_handles ) ) {
                    if ( 'scripts' == $type ) {
                        wp_enqueue_script( $handle, $file_url, $dependencies, filemtime( $file_path ), 'footer' == $location );
                        $enqueued_asset_handles[] = $handle;
                    } elseif ( 'stylesheets' == $type ) {
                        wp_enqueue_style( $handle, $file_url, $dependencies, filemtime( $file_path ) );
                        $enqueued_asset_handles[] = $handle;
                    }
                }
            } elseif ( 'internal' == $source ) {
                $file_path = $this->get_processed_asset_filepath( $asset );
                
                if ( false === $file_path ) {
                    continue;
                }

                echo 'scripts' == $type ? '<script>' : ( 'stylesheets' == $type ? '<style>' : '' );
                echo file_get_contents( $file_path );
                echo 'scripts' == $type ? '</script>' : ( 'stylesheets' == $type ? '</style>' : '' );
            }
        }
    }

    private function get_asset_dependencies( Asset $asset ) 
    {
        $dependencies = array();

        $header = $this->get_header_values( $asset );

        if ( $header && isset( $header[ 'Requires' ] ) ) {
            $dependencies = explode( ',', $header[ 'Requires' ] );
            $dependencies = array_map( 'trim', $dependencies );
            $dependencies = array_filter( $dependencies, function( $dependency ) {
                return $dependency != '';
            });
        }

        return $dependencies;
    }

    private function get_processed_asset_filepath( Asset $asset, string $store_as_filename = '' )
    {
        if ( ! isset( $this->extension_to_processor[ $asset->get_extension() ] ) ) {
            return false;
        }

        $processor_class = $this->extension_to_processor[ $asset->get_extension() ];

        $processed_file_path = $processor_class::get_processed_filepath( $asset );

        if ( false === $processed_file_path ) {
            return false;
        }

        if ( '' !== $store_as_filename && $processed_file_path !== $asset->get_absolute_filepath() ) {
            $store_extension = $asset->get_type() === 'scripts' ? 'js' : 'css';

            $processed_file_path = $this->store_processed_file(
                $store_as_filename, 
                $processed_file_path,
                $store_extension
            );
        }

        return $processed_file_path;
    }

    private function get_header_values( Asset $asset ) 
    {
        if ( ! isset( $this->extension_to_processor[ $asset->get_extension() ] ) ) {
            return false;
        }

        $processor_class = $this->extension_to_processor[ $asset->get_extension() ];

        return $processor_class::get_header_values( $asset );
    }

    private function store_processed_file( string $name, string $path, string $extension ) 
    {
        $serve_dir = WP_CONTENT_DIR . '/enqueueror';

        $name_parts = explode( '/', $name );

        $name_parts = array_filter($name_parts, function( $name_part ) {
            return '' != $name_part;
        });

        if ( count( $name_parts ) > 1 ) {
            $serve_dir .= '/' . implode( '/', array_slice( $name_parts, 0, -1 ) );
        }

        $name = end( $name_parts );

        $hash_separator = '-';

        if ( false === wp_mkdir_p( $serve_dir ) ) {
            return false;
        }

        static $basename_without_hash_to_filepaths = null;
        
        if ( null === $basename_without_hash_to_filepaths ) {
            foreach ( glob( "$serve_dir/*.$extension" ) as $filepath ) {
                $basename = basename( $filepath );
                $basename_parts = explode( $hash_separator, $basename );
                $name_part = implode( $hash_separator, array_slice( $basename_parts, 0, count( $basename_parts ) - 1 ) );
                $basename_without_hash = $name_part . $extension;
                $basename_without_hash_to_filepaths[ $basename_without_hash ][] = $serve_dir . DIRECTORY_SEPARATOR . $basename;
            }
        }

        $hash = md5_file( $path );

        $serve_file_path = $serve_dir . DIRECTORY_SEPARATOR . $name . $hash_separator . $hash . '.' . $extension;

        if ( ! file_exists( $serve_file_path ) ) {
            $basename_without_hash = $name . $extension;

            if ( isset( $basename_without_hash_to_filepaths[ $basename_without_hash ] ) ) {
                foreach ( $basename_without_hash_to_filepaths[ $basename_without_hash ] as $expired_asset_filepath) {
                    unlink( $expired_asset_filepath );
                    // $this->logger->debug( "Deleted processed file '" . $expired_asset_filepath . "' for handle '$name'" );
                }
            }

            copy( $path, $serve_file_path );

            $basename_without_hash_to_filepaths[ $basename_without_hash ][] = $serve_file_path;

            // $this->logger->debug( "Storing processed filename '$serve_file_path' for handle '$name'" );
        }

        // $this->logger->debug( "Returning processed filename '$serve_file_path' for handle '$name'" );

        return $serve_file_path;
    }
    
    /**
     * Translates an absolute filesystem path to its equivalent public URL.
     * 
     * @param string $path The filesystem path to return a public URL for.
     * @return string the absolute public URL corresponding to the path.
     */
    private function get_url_from_path( string $path ) 
    {
        $url = str_replace(
            wp_normalize_path( untrailingslashit( ABSPATH ) ), 
            site_url(), 
            wp_normalize_path( $path )
        );

        return esc_url_raw( $url );
    }

    /**
     * Should be called in WP asset enqueueing stage, to output assets that should be enqueued using WP enqueuing 
     * mechanisms.
     * 
     * @return void
     */
    public function output_enqueueable()
    {
        $this->enqueue( 'head', [ 'enqueue' ] );
    }

    /**
     * Should be called when generating the <head> HTML section, to output assets that should exist in <head> HTML 
     * section without using WP enqueuing mechanisms.
     * 
     * @return void
     */
    public function output_head_printed()
    {
        $this->enqueue( 'head', [ 'print' ] );
    }

    /**
     * Should be called when generating the last part of the <body> HTML section, to output assets that should exist in 
     * <body> HTML section.
     * 
     * @return void
     */
    public function output_footer()
    {
        $this->enqueue( 'footer', [ 'enqueue', 'print' ] );
    }
}