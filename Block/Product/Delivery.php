<?php
/**
 * Copyright © 2016 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Block\Product;

/**
 * Class Delivery
 *
 * @package FireGento\MageSetup\Block\Product
 */
class Delivery extends \Magento\Catalog\Block\Product\View\Description
{
    /**
     * Retrieve current product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->getData('product');
    }
}
