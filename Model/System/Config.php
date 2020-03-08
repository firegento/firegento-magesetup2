<?php
/**
 * Copyright © 2016 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */
namespace FireGento\MageSetup\Model\System;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 *
 * @package FireGento\MageSetup\Model\System
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
        $euCountries = $this->scopeConfig->getValue('general/country/eu_countries');

        return explode(',', $euCountries);
    }

    /**
     * Including shipping costs
     *
     * @return bool
     */
    public function isIncludingShippingCosts()
    {
        return (bool)$this->scopeConfig->getValue('catalog/price/including_shipping_costs');
    }

    /**
     * Get shipping cost url
     *
     * @return string|bool
     */
    public function getShippingCostUrl()
    {
        $identifier = $this->scopeConfig->getValue('catalog/price/cms_page_shipping');
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
        return (bool)$this->scopeConfig->getValue('catalog/frontend/display_delivery_time');
    }
}
