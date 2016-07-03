<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\System\Config\Source\Tax;

use FireGento\MageSetup\Model\Config;

/**
 * Class Dynamic
 *
 * @package FireGento\MageSetup\Model\System\Config\Source\Tax
 */
class Dynamic implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options = null;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $options = [
                [
                    'value' => Config::DYNAMIC_TYPE_DEFAULT,
                    'label' => __('No dynamic shipping tax caluclation')
                ],
                [
                    'value' => Config::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX,
                    'label' => __('Use the highest product tax')
                ]
            ];

            $this->options = $options;
        }

        return $this->options;
    }
}
