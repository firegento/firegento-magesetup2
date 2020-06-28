<?php
/**
 * Copyright © 2020 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Model\System\Config\Source\Tax;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Source model for Dynamic taxes.
 */
class Dynamic implements OptionSourceInterface
{
    public const DYNAMIC_TYPE_SHIPPING_TAX_DEFAULT = 0;
    public const DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX = 1;

    /**
     * @var array
     */
    private $options;

    /**
     * Method for returning options array
     *
     * @return array|array[]
     */
    public function toOptionArray(): array
    {
        if (null === $this->options) {
            $options = [
                [
                    'value' => self::DYNAMIC_TYPE_SHIPPING_TAX_DEFAULT,
                    'label' => __('No dynamic shipping tax calculation')
                ],
                [
                    'value' => self::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX,
                    'label' => __('Use the highest product tax')
                ]
            ];

            $this->options = $options;
        }

        return $this->options;
    }
}
