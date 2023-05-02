<?php

namespace panastasiadist\Enqueueror\Utilities;

use Exception;

class Filesystem
{
	/**
	 * Returns an array of absolute filesystem paths to files found recursively under the provided directory path.
	 *
	 * @param string $directory_path The absolute filesystem path to a directory.
	 *
	 * @return string[] An array of absolute filesystem paths.
	 */
	private static function get_file_paths( string $directory_path ): array
	{
		if ( ! is_readable( $directory_path ) ) {
			return array();
		}

		// Stored in ascending order.
		$file_paths = array();
		$file_directory_paths = array();

		foreach ( scandir( $directory_path, SCANDIR_SORT_ASCENDING ) as $basename ) {
			if ( '.' === $basename || '..' === $basename ) {
				continue;
			}

			$path = $directory_path . DIRECTORY_SEPARATOR . $basename;

			if ( is_file( $path ) ) {
				$file_paths[] = $path;
			} elseif ( is_dir( $path ) ) {
				$file_directory_paths[] = $path;
			}
		}

		// Process the directories found after having processed the files in the current folder in order to enforce
		// ascending depth.
		foreach ( $file_directory_paths as $path ) {
			$sub_files = self::get_file_paths( $path );

			if ( false !== $sub_files ) {
				$file_paths = array_merge( $file_paths, $sub_files );
			}
		}

		return $file_paths;
	}

	/**
	 * Recursively searches for files under the provided directory path, returning information only for files whose
	 * basename matches the provided regex.
	 *
	 * @param string $directory_path The directory's path to search for files.
	 * @param string $filename_regex The regex applied to each file's basename.
	 *
	 * @return array An array of associative arrays, each one containing information about a file.
	 */
	public static function get_files( string $directory_path, string $filename_regex ): array
	{
		$matched_files = array();

		$file_paths = self::get_file_paths( $directory_path );

		foreach ( $file_paths as $file_path ) {
			$info = pathinfo( $file_path );

			if ( preg_match( $filename_regex, $info[ 'basename' ] ) ) {
				$matched_files[] = $info;
			}
		}

		return $matched_files;
	}
}