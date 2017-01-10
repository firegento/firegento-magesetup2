<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Plugin\Catalog;

class ListProductPlugin
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve product details html
     *
     * @param \Magento\Catalog\Block\Product\ListProduct
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product)
    {
        $result = $proceed($product);

        $deliveryBlock = $subject->getLayout()->getBlock('product.info.delivery');

        if ($deliveryBlock) {
            $deliveryBlock->setProduct($product);

            if ((bool) $this->_scopeConfig->getValue(
                'catalog/frontend/display_delivery_time',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
                $result = $deliveryBlock->toHtml() . $result;
            }
        }

        return $result;
    }
}
