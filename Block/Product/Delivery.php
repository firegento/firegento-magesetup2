<?php
/**
 * Copyright © 2015 FireGento e.V. - All rights reserved.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Block\Product;

class Delivery extends \Magento\Catalog\Block\Product\View\Description
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product = null;

    /**
     * Retrieve current product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->getData('product');
        }
        return $this->_product;
    }
}
