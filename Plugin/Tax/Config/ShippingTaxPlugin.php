<?php

declare(strict_types=1);

namespace FireGento\MageSetup\Plugin\Tax\Config;

use FireGento\MageSetup\Model\Config as FireGentoConfig;
use FireGento\MageSetup\Model\System\Config\Source\Tax\Dynamic as FireGentoSource;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Calculation\Proxy;
use Magento\Tax\Model\Config;

/**
 * Class ShippingTaxPlugin
 *
 * FireGento\MageSetup\Plugin\Tax\Config
 */
class ShippingTaxPlugin
{

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
     * @var FireGentoConfig
     */
    private $config;

    /**
     * Constructor class
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Cart                 $cart
     * @param Session              $customerSession
     * @param GroupRepository      $groupRepository
     * @param FireGentoConfig      $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Cart $cart,
        Session $customerSession,
        GroupRepository $groupRepository,
        FireGentoConfig $config
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->cart            = $cart;
        $this->customerSession = $customerSession;
        $this->groupRepository = $groupRepository;
        $this->config          = $config;
    }

    /**
     * After plugin for \Magento\Tax\Model\Config::getShippingTaxClass
     *
     * @param Config                     $config
     * @param int                        $shippingTaxClass
     * @param null|string|bool|int|Store $store
     *
     * @return bool|int|mixed
     */
    public function afterGetShippingTaxClass(Config $config, int $shippingTaxClass, $store = null)
    {
        $dynamicType = $this->getDynamicShippingConfigPath($store);
        $quoteItems  = $this->cart->getItems();

        // If the default behaviour was configured or there are no products in cart, use default tax class id
        if ($dynamicType === FireGentoSource::DYNAMIC_TYPE_SHIPPING_TAX_DEFAULT || count($quoteItems) === 0) {
            return $shippingTaxClass;
        }

        $taxClassId = false;

        // Retrieve the highest product tax class
        if ($dynamicType === FireGentoSource::DYNAMIC_TYPE_HIGHEST_PRODUCT_TAX) {
            $taxClassId = $this->getHighestProductTaxClassId($quoteItems, $store);
        }

        // If no tax class id was found, use default one
        if (!$taxClassId) {
            $taxClassId = $shippingTaxClass;
        }

        return $taxClassId;
    }

    /**
     * Method for getting highest product tax class id
     *
     * @param array                      $quoteItems
     * @param null|string|bool|int|Store $store
     *
     * @return bool|int
     */
    private function getHighestProductTaxClassId(array $quoteItems, $store)
    {
        $taxClassIds       = [];
        $highestTaxClassId = false;

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

        /**
         * Fetch the highest tax rate
         */
        ksort($taxClassIds);
        if (count($taxClassIds) > 0) {
            $highestTaxClassId = array_pop($taxClassIds);
        }
        if (!$highestTaxClassId || $highestTaxClassId === null) {
            return false;
        }

        return $highestTaxClassId;
    }

    /**
     * Method for getting tax prcentage
     *
     * @param int                        $productTaxClassId
     * @param null|string|bool|int|Store $store
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
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

    /**
     * Method for retrieving dynamic config shipping path
     *
     * @param null|string|bool|int|Store $store
     *
     * @return int
     */
    private function getDynamicShippingConfigPath($store = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'tax/classes/dynamic_shipping_tax_class',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
