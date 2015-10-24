<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;

/**
 * Interface SubProcessorInterface
 *
 * @package Seat\IntegrationKM\Model\Product\SubProcessor
 */
interface SubProcessorInterface
{
    /**
     * @param Config $config
     * @return void
     */
    public function process(Config $config);
}
