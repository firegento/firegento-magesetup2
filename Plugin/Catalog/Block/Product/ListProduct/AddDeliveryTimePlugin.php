<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Plugin\Catalog\Block\Product\ListProduct;

use FireGento\MageSetup\Model\System\Config;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * Plugin for adding the delivery time to the product details on the product listing pages.
 */
class AddDeliveryTimePlugin
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * ListProductPlugin constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Retrieve product details html
     *
     * @param ListProduct $subject
     * @param string      $productDetailsHtml
     * @param Product     $product
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterGetProductDetailsHtml(ListProduct $subject, string $productDetailsHtml, Product $product)
    {
        $deliveryBlock = $subject->getLayout()->getBlock('product.info.delivery');
        if (!$deliveryBlock) {
            return $productDetailsHtml;
        }

        $deliveryBlock->setProduct($product);

        if ($this->config->isDisplayDeliveryTimeOnProductListing()) {
            $productDetailsHtml .= $deliveryBlock->toHtml();
        }

        return $productDetailsHtml;
    }
}
