<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
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
     * @param WriterInterface $configWriter
     */
    public function __construct(WriterInterface $configWriter)
    {
        parent::__construct($configWriter);
    }

    /**
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
