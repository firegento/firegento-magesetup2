<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class SystemConfigSubProcessor
 *
 * @package FireGento\MageSetup\Model\Setup\SubProcessor
 */
class SystemConfigSubProcessor extends AbstractSubProcessor
{
    /**
     * SystemConfigSubProcessor constructor.
     *
     * @param WriterInterface $configWriter
     */
    public function __construct(WriterInterface $configWriter)
    {
        parent::__construct($configWriter);
    }

    /**
     * Process
     *
     * @param Config $config
     * @return void
     */
    public function process(Config $config)
    {
        $configData = $config->getSystemConfig();
        if (count($configData)) {
            foreach ($configData as $key => $value) {
                $path = str_replace('__', '/', $key);
                $this->saveConfigValue($path, $value);
            }
        }
    }
}
