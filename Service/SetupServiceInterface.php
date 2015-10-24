<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
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
     * @return void
     */
    public function execute();

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);
}
