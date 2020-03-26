<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

namespace FireGento\MageSetup\Plugin\Pricing;

/**
 * Class AroundRenderPlugin
 *
 * @package FireGento\MageSetup\Model\Plugin
 */
class AroundRenderPlugin
{
    /** @const settings string to enable the block below price */
    const ENABLED_DISPLAY_BELOW_PRICE_XML = 'catalog/price/display_block_below_price';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \FireGento\MageSetup\Helper\Data
     */
    protected $helper;

    /**
     * AroundRenderPlugin constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \FireGento\MageSetup\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \FireGento\MageSetup\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * Around render
     *
     * @param \Magento\Framework\Pricing\Render $subject
     * @param \Closure $proceed
     * @param mixed $priceCode
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     * @param array $arguments
     * @return mixed|string
     */
    public function aroundRender(
        \Magento\Framework\Pricing\Render $subject,
        \Closure $proceed,
        $priceCode,
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        array $arguments = []
    ) {
        $returnValue = $proceed($priceCode, $saleableItem, $arguments);
        if (!$this->helper->getConfigValue(self::ENABLED_DISPLAY_BELOW_PRICE_XML)) {
            return $returnValue;
        }
        if (trim($returnValue) != '' && $saleableItem->getTypeId() !== 'grouped') {
            $block = $subject->getLayout()->getBlock('magesetup.product.price.details');
            if ($block) {
                $block->setSaleableItem($saleableItem);
                $returnValue = $returnValue . $block->toHtml();
            }
        }

        return $returnValue;
    }
}
