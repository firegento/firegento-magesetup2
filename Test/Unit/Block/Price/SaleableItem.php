<?php
/**
 * Copyright © 2026 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Test\Unit\Block\Price;

abstract class SaleableItem implements \Magento\Framework\Pricing\SaleableInterface
{
    /**
     * Get product type ID.
     *
     * @return string
     */
    abstract public function getTypeId();

    /**
     * Get tax percent.
     *
     * @return float|null
     */
    abstract public function getTaxPercent();

    /**
     * Get tax class ID.
     *
     * @return int|null
     */
    abstract public function getTaxClassId();
}
