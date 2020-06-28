<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use FireGento\MageSetup\Model\Config;

/**
 * Interface for the different steps.
 */
interface SubProcessorInterface
{
    /**
     * Process
     *
     * @param Config $config
     * @return void
     */
    public function process(Config $config);
}
