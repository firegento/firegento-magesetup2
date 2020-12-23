<?php
declare(strict_types=1);

namespace FireGento\MageSetup\Model\Sales\Total\Quote;

use Magento\Customer\Api\Data\AddressInterfaceFactory as CustomerAddressFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory as CustomerAddressRegionFactory;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;
use Magento\Tax\Api\Data\QuoteDetailsInterface;
use Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemExtensionInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class ShippingPreprocessor extends CommonTaxCollector
{

    /** @var Calculation */
    private Calculation $calculationTool;

    /** @var TaxClassManagementInterface */
    private TaxClassManagementInterface $taxClassManagement;

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
        TaxHelper $taxHelper = null,
        QuoteDetailsItemExtensionInterfaceFactory $quoteDetailsItemExtensionInterfaceFactory = null
    ) {
        parent::__construct($taxConfig, $taxCalculationService, $quoteDetailsDataObjectFactory, $quoteDetailsItemDataObjectFactory, $taxClassKeyDataObjectFactory, $customerAddressFactory, $customerAddressRegionFactory, $taxHelper, $quoteDetailsItemExtensionInterfaceFactory);
        $this->calculationTool    = $calculationTool;
        $this->taxClassManagement = $taxClassManagement;
    }

    /**
     * Calculate tax on product items. The result will be used to determine shipping
     * and discount later.
     *
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param AddressTotal                $total
     *
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        AddressTotal $total
    ) {
        $items = $shippingAssignment->getItems();
        if (!$items) {
            return $this;
        }

        $store            = $quote->getStore();
        $priceIncludesTax = $this->_config->priceIncludesTax($store);

        //Setup taxable items
        $itemDataObjects = $this->mapItems($shippingAssignment, $priceIncludesTax, false);
        $quoteDetails    = $this->prepareQuoteDetails($shippingAssignment, $itemDataObjects);

        $highestTaxRateId = $this->getHighestTaxRate($quoteDetails, $store->getStoreId());

        $total->setData('highest_product_tax_rate_tax_class_id', $highestTaxRateId);

        return $this;
    }

    /**
     * Populate QuoteDetails object from quote address object
     *
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param QuoteDetailsItemInterface[] $itemDataObjects
     *
     * @return QuoteDetailsInterface
     */
    protected function prepareQuoteDetails(ShippingAssignmentInterface $shippingAssignment, $itemDataObjects)
    {
        $quoteDetails = parent::prepareQuoteDetails($shippingAssignment, $itemDataObjects);

//        //Set default customer tax class
//        $quoteDetails->setCustomerTaxClassKey(
//            $this->taxClassKeyDataObjectFactory->create()
//                ->setType(TaxClassKeyInterface::TYPE_ID)
//                ->setValue($this->getDefaultCustomerTaxClassId())
//        );

        return $quoteDetails;
    }

    protected function getHighestTaxRate(QuoteDetailsInterface $quoteDetails, $storeId)
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

//            $rate      = $this->calculationTool->getRate($taxRateRequest);
            $storeRate = $this->calculationTool->getStoreRate($taxRateRequest, $storeId);

            if ($storeRate > $maxTaxRate) {
                $maxTaxRate        = $storeRate;
                $maxTaxRateClassId = $itemTaxClassId;
            }
        }

        return $maxTaxRateClassId;
    }

    private function getDefaultCustomerTaxClassId(): int
    {
        /** todo read from store config */
        return 7;
        return $this->calculationTool->getDefaultCustomerTaxClass();
    }

    /** {@inheritDoc} */
    public function fetch(Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return null;
    }
}
