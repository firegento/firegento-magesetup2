<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Service;

use FireGento\MageSetup\Model\Setup\SubProcessor\SubProcessorPool;
use FireGento\MageSetup\Model\Config;
use Magento\Framework\App\Cache\Manager as CacheManager;

/**
 * Class SetupService
 *
 * @package FireGento\MageSetup\Service
 */
class SetupService implements SetupServiceInterface
{
    /**
     * @var \FireGento\MageSetup\Model\ConfigInterface
     */
    private $config;

    /**
     * @var SubProcessorPool
     */
    private $subProcessorPool;

    /**
     * @var array
     */
    private $subProcessorCodes;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param Config           $config
     * @param CacheManager     $cacheManager
     * @param SubProcessorPool $subProcessorPool
     * @param array            $subProcessorCodes
     */
    public function __construct(
        Config $config,
        CacheManager $cacheManager,
        SubProcessorPool $subProcessorPool,
        array $subProcessorCodes = []
    ) {
        $this->config = $config;
        $this->cacheManager = $cacheManager;
        $this->subProcessorPool = $subProcessorPool;

        if (empty($subProcessorCodes)) {
            $subProcessorCodes = $this->subProcessorPool->getSubProcessorCodes();
        }
        $this->subProcessorCodes = $subProcessorCodes;
    }

    /**
     * @return void
     */
    public function execute()
    {
        foreach ($this->subProcessorCodes as $subProcessorCode) {
            $subProcessor = $this->subProcessorPool->get($subProcessorCode);
            $subProcessor->process($this->config);
        }

        $this->cacheManager->clean(['config', 'full_page']);
    }
}
