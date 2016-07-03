<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Plugin\Tax\Config;

use Magento\Store\Model\Store;
use FireGento\MageSetup\Model\Config;

/**
 * Class AroundGetShippingTaxClassPlugin
 *
 * @package FireGento\MageSetup\Plugin\Tax\Config
 */
class AroundGetShippingTaxClassPlugin
{
    const CONFIG_PATH_DYNAMIC_SHIPPING_TAX_CLASS = 'tax/classes/dynamic_shipping_tax_class';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Customer\Model\ResourceModel\GroupRepository
     */
    private $groupRepository;
    /**
     * @var \Magento\Tax\Model\Calculation\Proxy
     */
    private $taxCalculation;

    /**
     * AroundGetShippingTaxClassPlugin constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfig
     * @param \Magento\Checkout\Model\Cart                          $cart
     * @param \Magento\Customer\Model\Session                       $customerSession
     * @param \Magento\Customer\Model\ResourceModel\GroupRepository $groupRepository
     * @param \Magento\Tax\Model\Calculation\Proxy                  $taxCalculation
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\GroupRepository $groupRepository,
        \Magento\Tax\Model\Calculation\Proxy $taxCalculation
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->taxCalculation = $taxCalculation;
    }

    /**
     * @param \Magento\Tax\Model\Config  $subject
     * @param \Closure                   $proceed
     * @param null|string|bool|int|Store $store
     * @return mixed
     */
    public function aroundGetShippingTaxClass(\Magento\Tax\Model\Config $subject, \Closure $proceed, $store)
    {
        $handle = \fopen('abc.log', 'w+');

        $dynamicType = (int)$this->scopeConfig->getValue(
            self::CONFIG_PATH_DYNAMIC_SHIPPING_TAX_CLASS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        // TODO: Check for admin quote

        $quoteItems = $this->cart->getItems();

        // If the default behaviour was configured or there are no products in cart, use default tax class id
        if ($dynamicType == Config::DYNAMIC_TYPE_DEFAULT || count($quoteItems) == 0) {
            return $proceed($store);
        }

        $taxClassId = false;

        // Retrieve the highest product tax class
        if ($dynamicType == Config::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX) {
            $taxClassId = $this->getHighestProductTaxClassId($quoteItems, $store);
        }

        // If no tax class id was found, use default one
        if (!$taxClassId) {
            $taxClassId = $proceed($store);
        }

        return $taxClassId;
    }

    /**
     * @param array                      $quoteItems
     * @param null|string|bool|int|Store $store
     * @return bool|mixed|null
     */
    private function getHighestProductTaxClassId($quoteItems, $store)
    {
        $taxClassIds = [];
        $highestTaxRate = null;

        foreach ($quoteItems as $quoteItem) {
            /** @var $quoteItem \Magento\Quote\Model\Quote\Item */
            if ($quoteItem->getParentItem()) {
                continue;
            }

            // Retrieve the tax percent
            $taxPercent = $quoteItem->getTaxPercent();
            if (!$taxPercent) {
                $taxPercent = $this->getTaxPercent($quoteItem->getTaxClassId(), $store);
            }

            // Add the tax class
            if (is_float($taxPercent) && !in_array($taxPercent, $taxClassIds)) {
                $taxClassIds[$taxPercent] = $quoteItem->getTaxClassId();
            }
        }

        // Fetch the highest tax rate
        ksort($taxClassIds);
        if (count($taxClassIds) > 0) {
            $highestTaxRate = array_pop($taxClassIds);
        }
        if (!$highestTaxRate || is_null($highestTaxRate)) {
            return false;
        }

        return $highestTaxRate;
    }

    /**
     * @param int                        $productTaxClassId
     * @param null|string|bool|int|Store $store
     * @return float|int
     */
    private function getTaxPercent($productTaxClassId, $store)
    {
        $groupId = $this->customerSession->getCustomerGroupId();
        $group = $this->groupRepository->getById($groupId);
        $customerTaxClassId = $group->getTaxClassId();

        $request = $this->taxCalculation->getRateRequest(null, null, $customerTaxClassId, $store);
        $request->setData('product_class_id', $productTaxClassId);

        $taxPercent = $this->taxCalculation->getRate($request);
        if (!$taxPercent) {
            $taxPercent = 0;
        }

        return $taxPercent;
    }
}
