<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror\Base;

/**
 * Represents information about an asset file candidate for processing.
 */
class Asset
{
	private $type;
	private $extension;
	private $absolute_filepath;
	private $relative_filepath;
	private $basename;
	private $filename;
	private $context;
	private $language_code;
	private $flags;

	/**
	 * @param string $type The asset's type.
	 * @param string $extension The physical or composite, processor supported extension of the asset's file.
	 * @param string $absolute_filepath The absolute filesystem path to the asset's file.
	 * @param string $relative_filepath The filesystem path to the asset's file relatively to asset's root directory.
	 * @param string $filename The asset's file name without its extension.
	 * @param string $language_code The asset's targeted language code. Valid values are 'all' or a language code.
	 */
	public function __construct(
		string $type,
		string $extension,
		string $absolute_filepath,
		string $relative_filepath,
		string $filename,
		string $context,
		string $language_code,
		array $flags)
	{
		$this->type = $type;
		$this->extension = $extension;
		$this->absolute_filepath = $absolute_filepath;
		$this->relative_filepath = $relative_filepath;
		$this->filename = $filename;
		$this->context = $context;
		$this->language_code = $language_code;
		$this->flags = $flags;
		$this->basename = basename( $absolute_filepath );
	}

	/**
	 * Returns the asset's type.
	 *
	 * @return string
	 */
	public function get_type(): string
	{
		return $this->type;
	}

	/**
	 * Returns the asset's extension as passed in this instance.
	 * It is not necessary that the actual asset's file extension is returned.
	 * Actually, a composite, processor supported extension (ex css.php) may be returned.
	 *
	 * @return string
	 */
	public function get_extension(): string
	{
		return $this->extension;
	}

	/**
	 * Returns the absolute filesystem path to the asset's file.
	 *
	 * @return string
	 */
	public function get_absolute_filepath(): string
	{
		return $this->absolute_filepath;
	}

	/**
	 * Returns the filesystem path to the asset's file, relatively to assets' root directory.
	 *
	 * @return string
	 */
	public function get_relative_filepath(): string
	{
		return $this->relative_filepath;
	}

	/**
	 * Returns the asset's file name with its extension.
	 *
	 * @return string
	 */
	public function get_basename(): string
	{
		return $this->basename;
	}

	/**
	 * Returns the asset's file name without its extension.
	 *
	 * @return string
	 */
	public function get_filename(): string
	{
		return $this->filename;
	}

	/**
	 * Returns the asset's context.
	 *
	 * @return string Returns 'global' or 'current'.
	 */
	public function get_context(): string
	{
		return $this->context;
	}

	/**
	 * Returns the asset's targeted language code.
	 *
	 * @return string Returns 'all' or a language code.
	 */
	public function get_language_code(): string
	{
		return $this->language_code;
	}

	/**
	 * Returns a string array containing the asset's flags.
	 *
	 * @return string[]
	 */
	public function get_flags(): array
	{
		return $this->flags;
	}
}