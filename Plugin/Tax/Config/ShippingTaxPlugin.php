<?php

declare(strict_types=1);

namespace FireGento\MageSetup\Plugin\Tax\Config;

use FireGento\MageSetup\Model\Config as FireGentoConfig;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Calculation\Proxy;
use Magento\Tax\Model\Config;

class ShippingTaxPlugin
{
    public const CONFIG_PATH_DYNAMIC_SHIPPING_TAX_CLASS = 'tax/classes/dynamic_shipping_tax_class';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Proxy
     */
    private $taxCalculation;

    /**
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Cart                 $cart
     * @param Session              $customerSession
     * @param GroupRepository      $groupRepository
     * @param Proxy                $taxCalculation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Cart $cart,
        Session $customerSession,
        GroupRepository $groupRepository,
        Proxy $taxCalculation
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->cart            = $cart;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->taxCalculation  = $taxCalculation;
    }

    public function afterGetShippingTaxClass(Config $config, int $shippingTaxClass, $store = null)
    {
        $dynamicType = (int)$this->scopeConfig->getValue(
            self::CONFIG_PATH_DYNAMIC_SHIPPING_TAX_CLASS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        $quoteItems = $this->cart->getItems();

        // If the default behaviour was configured or there are no products in cart, use default tax class id
        if ($dynamicType === FireGentoConfig::DYNAMIC_TYPE_DEFAULT || count($quoteItems) === 0) {
            return $shippingTaxClass;
        }

        $taxClassId = false;

        // Retrieve the highest product tax class
        if ($dynamicType === FireGentoConfig::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX) {
            $taxClassId = $this->getHighestProductTaxClassId($quoteItems, $store);
        }

        // If no tax class id was found, use default one
        if (!$taxClassId) {
            $taxClassId = $shippingTaxClass;
        }

        return $taxClassId;
    }

    /**
     * @param array                      $quoteItems
     * @param null|string|bool|int|Store $store
     *
     * @return bool|mixed|null
     */
    private function getHighestProductTaxClassId($quoteItems, $store)
    {
        $taxClassIds    = [];
        $highestTaxRate = null;

        foreach ($quoteItems as $quoteItem) {
            /** @var $quoteItem Item */
            if ($quoteItem->getParentItem()) {
                continue;
            }

            // Retrieve the tax percent
            $taxPercent = $quoteItem->getTaxPercent();
            if (!$taxPercent) {
                $taxPercent = $this->getTaxPercent($quoteItem->getTaxClassId(), $store);
            }

            // Add the tax class
            if (($taxPercent) && !in_array($taxPercent, $taxClassIds)) {
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

    private function getTaxPercent(int $productTaxClassId, $store): int
    {
        $groupId            = $this->customerSession->getCustomerGroupId();
        $group              = $this->groupRepository->getById($groupId);
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
