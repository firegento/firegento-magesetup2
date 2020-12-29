<?php
/**
 * Copyright © 2020 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

declare(strict_types=1);

namespace FireGento\MageSetup\Model\Sales\Total\Quote;

use FireGento\MageSetup\Model\System\Config as SystemConfig;
use Magento\Customer\Api\Data\AddressInterfaceFactory as CustomerAddressFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory as CustomerAddressRegionFactory;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use Magento\Tax\Api\Data\QuoteDetailsInterface;
use Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

/**
 * Class ShippingPreprocessor
 *
 * FireGento\MageSetup\Model\Sales\Total\Quote
 */
class ShippingPreprocessor extends CommonTaxCollector
{
    /** @var Calculation */
    private $calculationTool;

    /** @var TaxClassManagementInterface */
    private $taxClassManagement;

    /**  @var SystemConfig */
    private $sysConfig;

    /**
     * @param Config                                         $taxConfig
     * @param TaxCalculationInterface                        $taxCalculationService
     * @param QuoteDetailsInterfaceFactory                   $quoteDetailsDataObjectFactory
     * @param QuoteDetailsItemInterfaceFactory               $quoteDetailsItemDataObjectFactory
     * @param TaxClassKeyInterfaceFactory                    $taxClassKeyDataObjectFactory
     * @param CustomerAddressFactory                         $customerAddressFactory
     * @param CustomerAddressRegionFactory                   $customerAddressRegionFactory
     * @param Calculation                                    $calculationTool
     * @param TaxClassManagementInterface                    $taxClassManagement
     * @param SystemConfig                                   $sysConfig
     * @param TaxHelper|null                                 $taxHelper
     * @param QuoteDetailsItemExtensionInterfaceFactory|null $quoteDetailsItemExtensionInterfaceFactory
     */
    public function __construct(
        Config $taxConfig,
        TaxCalculationInterface $taxCalculationService,
        QuoteDetailsInterfaceFactory $quoteDetailsDataObjectFactory,
        QuoteDetailsItemInterfaceFactory $quoteDetailsItemDataObjectFactory,
        TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory,
        CustomerAddressFactory $customerAddressFactory,
        CustomerAddressRegionFactory $customerAddressRegionFactory,
        Calculation $calculationTool,
        TaxClassManagementInterface $taxClassManagement,
        SystemConfig $sysConfig,
        TaxHelper $taxHelper = null,
        QuoteDetailsItemExtensionInterfaceFactory $quoteDetailsItemExtensionInterfaceFactory = null
    ) {
        parent::__construct(
            $taxConfig,
            $taxCalculationService,
            $quoteDetailsDataObjectFactory,
            $quoteDetailsItemDataObjectFactory,
            $taxClassKeyDataObjectFactory,
            $customerAddressFactory,
            $customerAddressRegionFactory,
            $taxHelper,
            $quoteDetailsItemExtensionInterfaceFactory
        );

        $this->calculationTool    = $calculationTool;
        $this->taxClassManagement = $taxClassManagement;
        $this->sysConfig          = $sysConfig;
    }

    /**
     * Finds the product tax class with highest tax rate applied to quote product items.
     *
     * The result is saved to be used as shipping tax rate.
     * The method is inspired by `Subtotal::collect()`
     *
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param AddressTotal                $total
     *
     * @return $this
     *
     * @see \Magento\Tax\Model\Sales\Total\Quote\Subtotal::collect()
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        AddressTotal $total
    ) {
        if (!$this->sysConfig->isDynamicShippingTaxClassActive()) {
            return $this;
        }

        $items = $shippingAssignment->getItems();
        if (!$items) {
            return $this;
        }

        $store            = $quote->getStore();
        $priceIncludesTax = $this->_config->priceIncludesTax($store);

        $itemDataObjects = $this->mapItems($shippingAssignment, $priceIncludesTax, false);
        $quoteDetails    = $this->prepareQuoteDetails($shippingAssignment, $itemDataObjects);

        $highestTaxRateId = $this->getHighestTaxRateClassId($quoteDetails, $store->getStoreId());

        $total->setData('highest_product_tax_rate_tax_class_id', $highestTaxRateId);

        return $this;
    }

    /**
     * Loops through quote items and finds the product tax class with highest tax rate applied to quote product items.
     *
     * This method is abridged version of calculation happening the original magento code.
     *
     * @param QuoteDetailsInterface $quoteDetails
     * @param int                   $storeId
     *
     * @return int|null
     *
     * @see \Magento\Tax\Model\TaxCalculation::calculateTax()
     */
    protected function getHighestTaxRateClassId(QuoteDetailsInterface $quoteDetails, $storeId)
    {
        $maxTaxRate        = 0.0;
        $maxTaxRateClassId = null;

        foreach ($quoteDetails->getItems() as $item) {
            $addressRateRequest = $this->calculationTool->getRateRequest(
                $quoteDetails->getShippingAddress(),
                $quoteDetails->getBillingAddress(),
                $this->getDefaultCustomerTaxClassId(),
                $storeId,
                $quoteDetails->getCustomerId()
            );

            $itemTaxClassId = (int)$this->taxClassManagement->getTaxClassId($item->getTaxClassKey());
            $taxRateRequest = $addressRateRequest->setProductClassId($itemTaxClassId);

            $storeRate = $this->calculationTool->getStoreRate($taxRateRequest, $storeId);

            if ($storeRate > $maxTaxRate) {
                $maxTaxRate        = $storeRate;
                $maxTaxRateClassId = $itemTaxClassId;
            }
        }

        return $maxTaxRateClassId;
    }

    /**
     * GetDefaultCustomerTaxClassId
     *
     * @return int
     */
    private function getDefaultCustomerTaxClassId(): int
    {
        return (int)$this->calculationTool->getDefaultCustomerTaxClass();
    }

    /**
     * Fetch (Retrieve data as array)
     *
     * @param Quote        $quote
     * @param AddressTotal $total
     *
     * @return array|null
     */
    public function fetch(Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return null;
    }
}
