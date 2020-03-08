<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Service;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface SetupServiceInterface
 *
 * @package FireGento\MageSetup\Service
 */
interface SetupServiceInterface
{
    /**
     * Setup service
     *
     * @return void
     */
    public function execute();

    /**
     * Set output
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);
}
