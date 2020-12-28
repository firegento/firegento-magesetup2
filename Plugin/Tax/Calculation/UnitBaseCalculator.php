<?php

declare(strict_types=1);

namespace FireGento\MageSetup\Plugin\Tax\Calculation;

class UnitBaseCalculator extends \Magento\Tax\Model\Calculation\UnitBaseCalculator
{
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
     */
    protected function calculatePriceInclTax($storePriceInclTax, $storeRate, $customerRate, $round = true)
    {
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

    protected function getDefaultCustomerRateForDestCountry()
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
