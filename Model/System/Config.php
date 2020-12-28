<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\System;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class for retrieving configuration values.
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    private $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PageFactory $pageFactory
    ) {
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->pageFactory = $pageFactory;
    }

    /**
     * Check whether specified country is in EU countries list
     *
     * @param  string $countryCode Country Code
     * @return bool Flag if country is an EU country
     */
    public function isCountryInEu($countryCode)
    {
        return in_array(strtoupper($countryCode), $this->getEuCountries());
    }

    /**
     * Get countries in the EU
     *
     * @return array EU Countries
     */
    public function getEuCountries()
    {
        $euCountries = $this->scopeConfig->getValue('general/country/eu_countries', ScopeInterface::SCOPE_STORE);

        return explode(',', $euCountries);
    }

    /**
     * Including shipping costs
     *
     * @return bool
     */
    public function isIncludingShippingCosts()
    {
        return (bool)$this->scopeConfig->getValue('catalog/price/including_shipping_costs', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get shipping cost url
     *
     * @return string|bool
     */
    public function getShippingCostUrl()
    {
        $identifier = $this->scopeConfig->getValue('catalog/price/cms_page_shipping', ScopeInterface::SCOPE_STORE);
        if (!$identifier) {
            return false;
        }

        /** @var \Magento\Cms\Model\Page $page */
        $page = $this->pageFactory->create();
        $page->setStoreId($this->storeManager->getStore()->getId());
        $page->load($identifier, 'identifier');

        if (!$page->getId()) {
            return false;
        }

        return $this->context->getUrlBuilder()->getUrl(null, ['_direct' => $page->getIdentifier()]);
    }

    /**
     * Display delivery time in product listing
     *
     * @return bool
     */
    public function isDisplayDeliveryTimeOnProductListing()
    {
        return (bool)$this->scopeConfig->getValue('catalog/frontend/display_delivery_time', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks if dynamic shipping tax class is active.
     * It means shop will take product tax class with highest rate from current cart product items
     * and then use it as shipping tax class for this cart/quote during checkout.
     *
     * @return bool
     */
    public function isDynamicShippingTaxClassActive()
    {
        return (bool)$this->scopeConfig->getValue('tax/classes/dynamic_shipping_tax_class');
    }

    /**
     * Checks if advanced cross-border trade is active.
     * Advanced cross-border trade means:
     *  * end prices are the same for end customers, no matter which tax rates are applied
     *  * business customers receive different net prices depending on their tax address
     *
     * @return bool
     */
    public function isAdvancedCrossBorderTradeActive()
    {
        return (bool)$this->scopeConfig->getValue('tax/calculation/cross_border_trade_advanced_enabled');
    }
}
