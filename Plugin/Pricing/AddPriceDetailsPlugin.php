<?php

namespace FireGento\MageSetup\Plugin\Pricing;

use FireGento\MageSetup\Helper\Data;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\SaleableInterface;

/**
 * Class AddPriceDetailsPlugin
 *
 * @package FireGento\MageSetup\Plugin\Pricing
 */
class AddPriceDetailsPlugin
{
    /** @const settings string to enable the block below price */
    const ENABLED_DISPLAY_BELOW_PRICE_XML = 'catalog/price/display_block_below_price';

    /**
     * Helper
     *
     * @var Data
     */
    protected $helper;

    /**
     * AddPriceDetailsPlugin constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Adds price details to price block.
     *
     * @param Render $subject
     * @param string $result
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterRender(Render $subject, $result, $priceCode, SaleableInterface $saleableItem)
    {
        if (!$this->shouldAddPriceDetails($result, $priceCode, $saleableItem)) {
            return $result;
        }
        $block = $subject->getLayout()->getBlock('magesetup.product.price.details');
        if ($block) {
            $block->setSaleableItem($saleableItem);
            $result .= $block->toHtml();
        }

        return $result;
    }

    /**
     * Checks if price details should be added.
     *
     * @param bool $result
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     *
     * @return bool
     */
    private function shouldAddPriceDetails($result, $priceCode, SaleableInterface $saleableItem): bool
    {
        if (!$this->helper->getConfigValue(self::ENABLED_DISPLAY_BELOW_PRICE_XML)) {
            return false;
        }

        if (trim($result) === '') {
            return false;
        }

        // price details are shown for each child product, not for the grouped product
        if ($saleableItem->getTypeId() === 'grouped') {
            return false;
        }

        // do not show the price details after the tier prices again - they are already added to the final price
        if ($priceCode === TierPrice::PRICE_CODE) {
            return false;
        }

        return true;
    }
}
