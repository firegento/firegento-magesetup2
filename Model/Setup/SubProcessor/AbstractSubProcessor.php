<?php
/**
 * Copyright © 2016 FireGento e.V.
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
     * AbstractSubProcessor constructor.
     *
     * @param WriterInterface $configWriter
     */
    public function __construct(WriterInterface $configWriter)
    {
        $this->configWriter = $configWriter;
    }

    /**
     * Save config value
     *
     * @param string $path
     * @param string $value
     * @param mixed $storeId
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
