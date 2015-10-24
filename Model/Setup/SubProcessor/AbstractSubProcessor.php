<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\Setup\SubProcessor;

use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class AbstractSubProcessor
 *
 * @package FireGento\MageSetup\Model\Setup\SubProcessor
 */
abstract class AbstractSubProcessor implements SubProcessorInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @param WriterInterface $configWriter
     */
    public function __construct(WriterInterface $configWriter)
    {
        $this->configWriter = $configWriter;
    }

    /**
     * @param string $path
     * @param string $value
     * @param null   $storeId
     */
    public function saveConfigValue($path, $value, $storeId = null)
    {
        if (null === $storeId || $storeId == 0) {
            $this->configWriter->save($path, $value);
        } else {
            $this->configWriter->save($path, $value, 'stores', $storeId);
        }
    }
}
