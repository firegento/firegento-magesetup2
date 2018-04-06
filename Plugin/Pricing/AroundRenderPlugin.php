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
     * @param \Magento\Framework\Pricing\Render            $subject
     * @param \Closure                                     $proceed
     * @param string                                       $priceCode
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     * @param array                                        $arguments
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        \Magento\Framework\Pricing\Render $subject,
        \Closure $proceed,
        $priceCode,
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        array $arguments = []
    ) {
        $returnValue = $proceed($priceCode, $saleableItem, $arguments);
        // if there is anything returned we should render the price details
        $shouldRender = trim($returnValue) != '';
        // tier price returns JavaScript template, so check if item actually
        // has tier prices
        if ($priceCode === 'tier_price') {
            $tierPrices = $saleableItem->getTierPrices();
            $shouldRender = $tierPrices !== null &&
                            is_array($tierPrices) &&
                            count($tierPrices) > 0;
        }
        if ($shouldRender) {
            $block = $subject->getLayout()->getBlock('magesetup.product.price.details');
            if ($block) {
                $block->setSaleableItem($saleableItem);
                $returnValue = $returnValue . $block->toHtml();
            }
        }

        return $returnValue;
    }
}
