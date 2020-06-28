<?php
/**
 * Copyright © 2020 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Model\System\Config\Source\Tax;

use FireGento\MageSetup\Model\Config as FireGentoConfig;
use Magento\Framework\Data\OptionSourceInterface;

class Dynamic implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    public function toOptionArray(): array
    {
        if (null === $this->options) {
            $options = [
                [
                    'value' => FireGentoConfig::DYNAMIC_TYPE_DEFAULT,
                    'label' => __('No dynamic shipping tax calculation')
                ],
                [
                    'value' => FireGentoConfig::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX,
                    'label' => __('Use the highest product tax')
                ]
            ];

            $this->options = $options;
        }

        return $this->options;
    }
}
