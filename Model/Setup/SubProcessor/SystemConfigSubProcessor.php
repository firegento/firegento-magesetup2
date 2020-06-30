<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;

/**
 * Class for processing the system config setup step.
 */
class SystemConfigSubProcessor extends AbstractSubProcessor
{
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
