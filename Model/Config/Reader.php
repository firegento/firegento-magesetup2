<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Config;

/**
 * Class Reader
 *
 * @package FireGento\MageSetup\Model\Config
 */
class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    // phpcs:ignore
    protected $_idAttributes = [
        '/magesetup/setup' => 'name',
    ];
}
