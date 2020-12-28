<?php
/**
 * Copyright © 2020 FireGento e.V.
 * See LICENSE.md bundled with this module for license details.
 */

declare(strict_types=1);

namespace FireGento\MageSetup\Model\Tax\Calculation;

use FireGento\MageSetup\Model\System\Config as SystemConfig;
use Magento\Framework\DataObject;
use Magento\Tax\Api\Data\AppliedTaxInterfaceFactory;
use Magento\Tax\Api\Data\AppliedTaxRateInterfaceFactory;
use Magento\Tax\Api\Data\TaxDetailsItemInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;

trait AdvancedCrossBorderCalculator
{
    /** @var SystemConfig */
    private SystemConfig $sysConfig;

    /**
     * @param TaxClassManagementInterface    $taxClassService
     * @param TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory
     * @param AppliedTaxInterfaceFactory     $appliedTaxDataObjectFactory
     * @param AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory
     * @param Calculation                    $calculationTool
     * @param Config                         $config
     * @param int                            $storeId
     * @param SystemConfig                   $sysConfig
     * @param DataObject|null                $addressRateRequest
     */
    public function __construct(
        TaxClassManagementInterface $taxClassService,
        TaxDetailsItemInterfaceFactory $taxDetailsItemDataObjectFactory,
        AppliedTaxInterfaceFactory $appliedTaxDataObjectFactory,
        AppliedTaxRateInterfaceFactory $appliedTaxRateDataObjectFactory,
        Calculation $calculationTool,
        Config $config,
        $storeId,
        SystemConfig $sysConfig,
        DataObject $addressRateRequest = null
    ) {
        parent::__construct($taxClassService,
            $taxDetailsItemDataObjectFactory,
            $appliedTaxDataObjectFactory,
            $appliedTaxRateDataObjectFactory,
            $calculationTool,
            $config,
            $storeId,
            $addressRateRequest = null);

        $this->sysConfig = $sysConfig;
    }

    /**
     * Check if tax rate is same as store tax rate
     *
     * @param float $rate
     * @param float $storeRate
     *
     * @return bool
     *
     * @see \Magento\Tax\Model\Calculation\AbstractCalculator::isSameRateAsStore
     */
    protected function isSameRateAsStore($rate, $storeRate)
    {
        if ($this->sysConfig->isAdvancedCrossBorderTradeActive()) {
            return false;
        } else {
            return parent::isSameRateAsStore($rate, $storeRate);
        }
    }

    /**
     * Given a store price that includes tax at the store rate, this function will back out the store's tax, and add in
     * the customer's tax.  Returns this new price which is the customer's price including tax.
     *
     * @param float   $storePriceInclTax
     * @param float   $storeRate
     * @param float   $customerRate
     * @param boolean $round
     *
     * @return float
     *
     * @see \Magento\Tax\Model\Calculation\AbstractCalculator::calculatePriceInclTax
     */
    protected function calculatePriceInclTax($storePriceInclTax, $storeRate, $customerRate, $round = true)
    {
        if (!$this->sysConfig->isAdvancedCrossBorderTradeActive()) {
            return parent::calculatePriceInclTax($storePriceInclTax, $storeRate, $customerRate, $round);
        }

        if ($this->config->crossBorderTradeEnabled($this->storeId)) {
            $defaultCustomerRate = $this->getDefaultCustomerRateForDestCountry();
            $defaultCustomerTax  = $this->calculationTool->calcTaxAmount($storePriceInclTax, $defaultCustomerRate, true, false);
            $priceExclTax        = $storePriceInclTax - $defaultCustomerTax;
        } else {
            $storeTax     = $this->calculationTool->calcTaxAmount($storePriceInclTax, $storeRate, true, false);
            $priceExclTax = $storePriceInclTax - $storeTax;
        }
        $customerTax = $this->calculationTool->calcTaxAmount($priceExclTax, $customerRate, false, false);

        $customerPriceInclTax = $priceExclTax + $customerTax;
        if ($round) {
            $customerPriceInclTax = $this->calculationTool->round($customerPriceInclTax);
        }
        return $customerPriceInclTax;
    }

    /**
     * simulate a customer with the same destination country as current customer but with default customer tax class.
     *
     * This is assuming that default tax class is for end customers (i.e. with VAT) and we can calculate a default
     * tax that is applied to customers on this destination, later we can substract that tax from end-price.
     *
     * @return float
     */
    protected function getDefaultCustomerRateForDestCountry(): float
    {
        $defaultCustomerTaxClass = $this->calculationTool->getDefaultCustomerTaxClass();

        $taxRateRequest             = $this->getAddressRateRequest();
        $originalCustomerTaxClassId = $taxRateRequest->getCustomerClassId();
        $taxRateRequest->setCustomerClassId($defaultCustomerTaxClass);
        $defaultCustomerRate = $this->calculationTool->getRate($taxRateRequest);
        $taxRateRequest->setCustomerClassId($originalCustomerTaxClassId);
        return $defaultCustomerRate;
    }
}
