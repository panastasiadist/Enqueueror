<?php 
declare(strict_types=1);

namespace panastasiadist\Enqueueror\Base;

/**
 * Represents information about an asset file which is candidate for processing.
 */
class AssetNameRule
{
    private $name = '';
    private $context = '';
    private $langcode = '';

    /**
     * @param string $name The asset's name.
     * @param string $target The asset's target. Valid values are 'global' or 'current'.
     * @param string $langcode The language's code targeted by the asset's name. 
     * Valid values are 'all' or a language code
     */
    public function __construct( string $name, string $context = 'current', string $langcode = 'all' )
    {
        $this->name = $name;
        $this->context = $context;
        $this->langcode = $langcode;
    }

    /**
     * Returns the regex pattern matching the name of assets represented by this rule.
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Return the context (global or current) supported by assets represented by this rule.
     * 
     * @return string Returns 'global' or 'current'.
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     * Returns the code of the language supported by assets represented by this rule.
     * 
     * @return string
     */
    public function get_langcode() 
    {
        return $this->langcode;
    }
}