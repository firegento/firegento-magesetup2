<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class EmailSubProcessor
 *
 * @package FireGento\MageSetup\Model\Setup\SubProcessor
 */
class EmailSubProcessor extends AbstractSubProcessor
{
    /**
     * EmailSubProcessor constructor.
     *
     * @param WriterInterface $configWriter
     */
    public function __construct(WriterInterface $configWriter)
    {
        parent::__construct($configWriter);
    }

    /**
     * Proccess
     *
     * @param Config $config
     * @return void
     */
    public function process(Config $config)
    {
        // TODO: Implement
    }
}
