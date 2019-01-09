<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Config;

/**
 * Class SchemaLocator
 *
 * @package FireGento\MageSetup\Model\Config
 */
class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * Merged config schema file name
     */
    const MERGED_CONFIG_SCHEMA = 'magesetup.xsd';

    /**
     * Per file validation schema file name
     */
    const PER_FILE_VALIDATION_SCHEMA = 'magesetup.xsd';

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    // phpcs:ignore
    protected $_schema = null;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    // phpcs:ignore
    protected $_perFileSchema = null;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $moduleDir = $moduleReader->getModuleDir('etc', 'FireGento_MageSetup');
        $this->_schema = $moduleDir . '/' . self::MERGED_CONFIG_SCHEMA;
        $this->_perFileSchema = $moduleDir . '/' . self::PER_FILE_VALIDATION_SCHEMA;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Get path to per file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->_perFileSchema;
    }
}
